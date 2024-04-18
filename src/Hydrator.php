<?php

declare(strict_types=1);

namespace PhoneBurner\ApiHandler;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @template T of object
 */
interface Hydrator
{
    /**
     * @phpstan-return T
     */
    public function create(ServerRequestInterface $request): ?object;

    /**
     * @phpstan-param T $object
     * @phpstan-return T
     */
    public function update(ServerRequestInterface $request, object $object): ?object;

    /**
     * @phpstan-param T $object
     * @phpstan-return T|null
     */
    public function delete(ServerRequestInterface $request, object $object): ?object;
}
