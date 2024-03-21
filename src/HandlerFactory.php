<?php

declare(strict_types=1);

namespace PhoneBurner\ApiHandler;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface HandlerFactory
{
    public function makeForRequest(ServerRequestInterface $request): RequestHandlerInterface;

    public function canHandle(ServerRequestInterface $request): bool;
}
