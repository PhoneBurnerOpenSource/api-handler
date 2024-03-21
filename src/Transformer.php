<?php

declare(strict_types=1);

namespace PhoneBurner\ApiHandler;

use Psr\Http\Message\ServerRequestInterface;

interface Transformer
{
    public function transform(object $object, ServerRequestInterface $request): mixed;
}
