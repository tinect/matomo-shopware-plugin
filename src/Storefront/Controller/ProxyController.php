<?php declare(strict_types=1);

namespace JinyaMatomo\Storefront\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ProxyController extends StorefrontController
{
    private SystemConfigService $systemConfigService;

    private int $cacheTime = 86400;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * Inspired by https://arnowelzel.de/samples/piwik-tracker-proxy.txt
     *
     * @Route("/mtmtrpr/", name="frontend.matomo.proxy", methods={"GET","POST"}, defaults={"XmlHttpRequest"=true,"csrf_protected"=false})
     */
    public function matomoProxy(Request $request): Response
    {
        $response = new Response();
        $response->headers->set('x-robots-tag', 'noindex,follow');

        $configs = $this->systemConfigService->get('JinyaMatomo.config');

        if (!isset($configs['matomoserver'], $configs['matomoauthtoken'])) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            return $response;
        }

        $matomoServer = 'https://' . \rtrim($configs['matomoserver'], '/') . '/';
        $authToken = $configs['matomoauthtoken'];

        $parameter = $request->query->all();
        if (empty($parameter)) {
            return $this->requestJs($response, $matomoServer);
        }

        $url = $matomoServer . 'matomo.php?cip=' . $this->getClientIp($request) . '&token_auth=' . $authToken . '&';
        /*if (isset($parameter['aname'])) {
            $parameter['action_name'] = $parameter['aname'];
            unset($parameter['aname']);
        }

        if (isset($parameter['ids'])) {
            $parameter['idsite'] = $parameter['ids'];
            unset($parameter['ids']);
        }*/

        foreach ($parameter as $key => $value) {
            $url .= $key . '=' . urlencode($value) . '&';
        }

        $response->headers->set('Content-Type', 'image/webp');
        $stream_options = ['http' => [
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'header' => 'Accept-Language: ' . str_replace(["\n", "\t", "\r"], '', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '') . "\r\n",
            'timeout' => 5,
        ]];
        $ctx = stream_context_create($stream_options);
        $response->setContent((string) file_get_contents($url, false, $ctx));

        return $response;
    }

    private function getClientIp(Request $request): ?string
    {
        $clientIp = $request->server->get('HTTP_X_FORWARDED_FOR');

        if (!$clientIp) {
            $clientIp = $request->getClientIp();
        }

        return $clientIp;
    }

    private function requestJs(Response $response, string $matomoServer): Response
    {
        $modifiedSince = 0;
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $modifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
            // strip any trailing data appended to header
            if (false !== ($semicolon = strpos($modifiedSince, ';'))) {
                $modifiedSince = strtotime(substr($modifiedSince, 0, $semicolon));
            }
        }

        $response->headers->set('Vary', 'Accept-Encoding');

        if ($modifiedSince && $modifiedSince < (time() - $this->cacheTime)) {
            $response->setStatusCode(Response::HTTP_NOT_MODIFIED);
            return $response;
        }

        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $response->headers->set('Expires', gmdate("D, d M Y H:i:s", time() + $this->cacheTime) . ' GMT');
        $response->headers->set('Pragma', 'cache');
        $response->headers->set('Cache-Control', 'max-age=' . $this->cacheTime);
        $response->headers->set('Content-Type', 'application/javascript; charset=UTF-8');
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, '1');

        if ($matomoJs = file_get_contents($matomoServer . 'matomo.js')) {
            //$matomoJs = str_replace(['"action_name="', 'idsite='], ['"aname="', 'ids='], $matomoJs);
            $response->setContent($matomoJs);
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
