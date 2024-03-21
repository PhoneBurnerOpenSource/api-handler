<?php

declare(strict_types=1);

namespace PhoneBurner\ApiHandler;

use PhoneBurner\Http\Message\ResponseWrapper;
use Psr\Http\Message\ResponseInterface;

class TransformableResponse implements ResponseInterface
{
    use ResponseWrapper;

    public function __construct(
        public readonly TransformableResource $transformable_resource,
        private readonly ResponseFactory $response_factory,
        private readonly int $status = 200,
    ) {
    }

    public function withTransformableResource(TransformableResource $transformable_resource): self
    {
        return new self(
            $transformable_resource,
            $this->response_factory,
            $this->status,
        );
    }

    public function getStatusCode(): int
    {
        if ($this->wrapped !== null) {
            return $this->getWrapped()->getStatusCode();
        }

        return $this->status;
    }

    private function getWrapped(): ResponseInterface
    {
        return $this->wrapped ??= $this->response_factory->make($this->transformable_resource, $this->status);
    }
}
