<?php

declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReadHandler extends DefaultHandler
{
    /**
     * @template T of object
     * @param Resolver<T> $resolver
     */
    public function __construct(
        private readonly Resolver $resolver,
        private readonly Transformer $transformer,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->getResponseFactory()->make(
            new TransformableResource(
                $this->resolver->resolve($request),
                $request,
                $this->transformer,
            ),
            200,
        );
    }
}
