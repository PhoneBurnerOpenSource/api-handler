<?php

declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @template T of object
 */
interface Resolver
{
    /**
     * @phpstan-return T
     */
    public function resolve(ServerRequestInterface $request): object;
}
