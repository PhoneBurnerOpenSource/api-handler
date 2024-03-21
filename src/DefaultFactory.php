<?php

declare(strict_types=1);

namespace PhoneBurner\ApiHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class DefaultFactory
{
    private static ResponseFactory $factory;

    public static function getDefaultResponseFactory(): ResponseFactory
    {
        return self::$factory ?? throw new \LogicException(
            'Default response factory not set, use setDefaultResponseFactory() or setFactories() to set it.',
        );
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
