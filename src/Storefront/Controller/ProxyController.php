<?php declare(strict_types=1);

namespace Tinect\Matomo\Storefront\Controller;

use Shopware\Core\Framework\Adapter\Cache\CacheValueCompressor;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Tinect\Matomo\MessageQueue\TrackMessage;
use Tinect\Matomo\Service\StaticHelper;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class ProxyController extends AbstractController
{
    private int $cacheTime = 86400;

    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly CacheInterface $cache,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    /**
     * Inspired by https://arnowelzel.de/samples/piwik-tracker-proxy.txt
     */
    #[Route(path: '/mtmtrpr', name: 'frontend.matomo.proxy', defaults: ['XmlHttpRequest' => true], methods: ['GET', 'POST'])]
    public function matomoProxy(Request $request): Response
    {
        $response = new Response();
        $response->headers->set('x-robots-tag', 'noindex,follow');

        $matomoServer = StaticHelper::getMatomoUrl($this->systemConfigService);

        if ($matomoServer === null) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);

            return $response;
        }

        $parameter = $request->query->all();
        if (empty($parameter)) {
            return $this->requestJs($response, $matomoServer);
        }

        $this->messageBus->dispatch(new TrackMessage(
            $request->getClientIp(),
            $request->server->getString('HTTP_USER_AGENT'),
            $request->server->getString('HTTP_ACCEPT_LANGUAGE'),
            time(),
            $parameter
        ));

        return $response;
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
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $this->cacheTime) . ' GMT');
        $response->headers->set('Pragma', 'cache');
        $response->headers->set('Cache-Control', 'max-age=' . $this->cacheTime);
        $response->headers->set('Content-Type', 'application/javascript; charset=UTF-8');
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, '1');

        $cacheValue = $this->cache->get('tinectmatomojs', function (ItemInterface $cacheItem) use ($matomoServer) {
            $cacheItem->expiresAfter(new \DateInterval('PT' . $this->cacheTime . 'S'));

            return CacheValueCompressor::compress(file_get_contents($matomoServer . 'matomo.js'));
        });

        $matomoJs = CacheValueCompressor::uncompress($cacheValue);

        if (\is_string($matomoJs)) {
            // $matomoJs = str_replace(['"action_name="', 'idsite='], ['"aname="', 'ids='], $matomoJs);
            $response->setContent($matomoJs);
        } else {
            $response->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $response;
    }
}
