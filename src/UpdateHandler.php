<?php

declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateHandler extends DefaultHandler
{
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
