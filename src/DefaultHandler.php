<?php
declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

abstract class DefaultHandler implements Handler
{
    private ResponseFactory $factory;
    public function setResponseFactory(ResponseFactory $factory): void
    {
        $this->factory = $factory;
    }

    protected function getResponseFactory(): ResponseFactory
    {
        return $this->factory ??= DefaultFactory::getDefaultResponseFactory();
    }

}