<?php

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseInterface;

class TransformableResponseFactory implements ResponseFactory
{
    public function __construct(
        private readonly ResponseFactory $realizing_factory,
    ) {
    }

    public function make(?TransformableResource $resource = null, int $code = 200): ResponseInterface
    {
        if (\is_null($resource)) {
            return $this->realizing_factory->make(null, $code);
        }

        return new TransformableResponse($resource, $this->realizing_factory, $code);
    }
}
