<?php

declare(strict_types=1);

namespace PhoneBurner\ApiHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateHandler extends DefaultHandler
{
    /**
     * @template T of object
     * @param Resolver<T> $resolver
     * @param Hydrator<T> $hydrator
     */
    public function __construct(
        private readonly Resolver $resolver,
        private readonly Hydrator $hydrator,
        private readonly Transformer $transformer,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->getResponseFactory()->make(
            new TransformableResource(
                $this->hydrator->update(
                    $request,
                    $this->resolver->resolve($request),
                ),
                $request,
                $this->transformer,
            ),
            200,
        );
    }
}
