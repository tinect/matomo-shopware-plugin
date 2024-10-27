<?php declare(strict_types=1);

namespace Tinect\Matomo\MessageQueue\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tinect\Matomo\MessageQueue\TrackMessage;

class CreateTrackMessageEvent
{
    public function __construct(
        public TrackMessage $message,
        public Request $request,
        public Response $response,
    ) {
    }
}