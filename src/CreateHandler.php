<?php

declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateHandler extends DefaultHandler
{
    /**
     * @template T of object
     * @param Hydrator<T> $hydrator
     */
    public function __construct(
        private readonly Hydrator $hydrator,
        private readonly Transformer $transformer,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->getResponseFactory()->make(
            new TransformableResource(
                $this->hydrator->create($request),
                $request,
                $this->transformer,
            ),
            201,
        );
    }
}
