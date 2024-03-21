<?php

declare(strict_types=1);

namespace PhoneBurnerTest\Api\Handler;

use PhoneBurner\Api\Handler\ResponseFactory;
use PhoneBurner\Api\Handler\TransformableResource;
use PhoneBurner\Api\Handler\TransformableResponseFactory;
use PhoneBurner\Api\Handler\Transformer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TransformableResponseFactoryTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    #[TestWith([200])]
    #[TestWith([201])]
    public function make_returns_TransformableResponse_configured_with_realizing_factory(int $status): void
    {
        $resource = new \stdClass();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $transformer = $this->prophesize(Transformer::class)->reveal();

        $realizing_factory = $this->prophesize(ResponseFactory::class);

        $transformable_resource = new TransformableResource($resource, $request, $transformer);

        $response = $this->prophesize(ResponseInterface::class);
        $realizing_factory->make($transformable_resource, $status)
            ->willReturn($response->reveal());

        $sut = new TransformableResponseFactory($realizing_factory->reveal());

        $transformable_response = $sut->make($transformable_resource, $status);

        $response->getReasonPhrase()->willReturn('test');

        self::assertSame($status, $transformable_response->getStatusCode());

        self::assertSame(
            'test',
            $transformable_response->getReasonPhrase(),
        );
    }

    #[Test]
    public function make_returns_empty_response_without_TransformableResponse(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $realizing_factory = $this->prophesize(ResponseFactory::class);
        $realizing_factory->make(null, 202)->willReturn($response->reveal());

        $sut = new TransformableResponseFactory($realizing_factory->reveal());

        self::assertSame(
            $response->reveal(),
            $sut->make(null, 202),
        );
    }
}
