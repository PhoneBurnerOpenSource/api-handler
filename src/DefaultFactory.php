<?php

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class DefaultFactory
{
    private static ResponseFactory $factory;

    public static function getDefaultResponseFactory(): ResponseFactory
    {
        if (! isset(self::$factory)) {
            throw new \RuntimeException('Default response factory not set, use setDefaultResponseFactory() or setFactories() to set it.');
        }

        return self::$factory;
    }

    public static function setDefaultResponseFactory(ResponseFactory $factory): void
    {
        self::$factory = $factory;
    }

    public static function setFactories(
        ResponseFactoryInterface $response_factory,
        StreamFactoryInterface $stream_factory,
    ): void {
        self::setDefaultResponseFactory(new TransformableResponseFactory(
            new SimpleResponseFactory($response_factory, $stream_factory),
        ));
    }
}
