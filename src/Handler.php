<?php

declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Server\RequestHandlerInterface;

interface Handler extends RequestHandlerInterface
{
    public function setResponseFactory(ResponseFactory $factory): void;
}
