<?php

declare(strict_types=1);

namespace PhoneBurner\ApiHandler;

use Psr\Http\Message\ServerRequestInterface;

readonly class TransformableResource
{
    public function __construct(
        public object $resource,
        public ServerRequestInterface $request,
        public Transformer $transformer,
    ) {
    }

    public function getContent(): mixed
    {
        return $this->transformer->transform($this->resource, $this->request);
    }
}
