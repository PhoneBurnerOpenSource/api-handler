<?php
declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatchMiddleware implements MiddlewareInterface
{
    public function __construct(public readonly HandlerFactory $factory)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        if (!$this->factory->canHandle($request)) {
            return $handler->handle($request);
        }

        $handler = $this->factory->makeForRequest($request);
        return $handler->handle($request);
    }
}