<?php

declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseInterface;

interface ResponseFactory
{
    public function make(?TransformableResource $resource = null, int $code = 200): ResponseInterface;
}
