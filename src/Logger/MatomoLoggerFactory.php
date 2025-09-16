<?php

namespace Tinect\Matomo\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Shopware\Core\Kernel;

class MatomoLoggerFactory
{
    private Kernel $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function create(): Logger
    {
        $logFile = $this->kernel->getLogDir() . '/matomo/' . date('y-m-d') . '/matomo.log';
        $logger = new Logger('matomo-logger');
        $handler = new StreamHandler($logFile);
        $logger->pushHandler($handler);
        $logger->pushProcessor(new PsrLogMessageProcessor());

        return $logger;
    }
}
