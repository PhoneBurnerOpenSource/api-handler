<?php

declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class SimpleResponseFactory implements ResponseFactory
{
    public function __construct(
        private readonly ResponseFactoryInterface $response_factory,
        private readonly StreamFactoryInterface $stream_factory,
    ) {
    }

    public function make(?TransformableResource $resource = null, int $code = 200): ResponseInterface
    {
        if (\is_null($resource)) {
            return $this->response_factory->createResponse($code);
        }

        $content = $resource->getContent();
        $response = $this->response_factory->createResponse($code);

        if (\is_null($content)) {
            return $response;
        }

        if (\is_resource($content)) {
            return $response->withBody($this->stream_factory->createStreamFromResource($content));
        }

        $content = match (true) {
            \is_string($content) => $content,
            default => \json_encode($content, \JSON_THROW_ON_ERROR) ?: ''
        };

        return $response->withBody($this->stream_factory->createStream($content));
    }
}
