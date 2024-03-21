<?php

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteHandler extends DefaultHandler
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
        $resource = $this->hydrator->delete(
            $request,
            $this->resolver->resolve($request),
        );

        if (\is_null($resource)) {
            return $this->getResponseFactory()->make(null, 204);
        }

        return $this->getResponseFactory()->make(
            new TransformableResource(
                $resource,
                $request,
                $this->transformer,
            ),
            200,
        );
    }
}
