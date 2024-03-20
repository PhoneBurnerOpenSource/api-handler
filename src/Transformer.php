<?php
declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface Transformer
{
    public function transform(object $object, ServerRequestInterface $request): mixed;
}