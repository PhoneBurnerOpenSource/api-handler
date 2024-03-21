<?php

declare(strict_types=1);

namespace PhoneBurner\Api\Handler;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @template T of object
 */
interface Hydrator
{
    /**
     * @return T
     */
    public function create(ServerRequestInterface $request): object;

    /**
     * @phpstan-param T $object
     * @return T
     */
    public function update(ServerRequestInterface $request, object $object): object;

    /**
     * @phpstan-param T $object
     * @return T|null
     */
    public function delete(ServerRequestInterface $request, object $object): ?object;
}
