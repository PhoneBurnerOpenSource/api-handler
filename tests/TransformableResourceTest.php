<?php

declare(strict_types=1);

namespace PhoneBurnerTest\Api\Handler;

use PhoneBurner\Api\Handler\TransformableResource;
use PhoneBurner\Api\Handler\Transformer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;

class TransformableResourceTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function getContent_transforms_resource(): void
    {
        $resource = new \stdClass();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = "a response";

        $transformer = $this->prophesize(Transformer::class);
        $transformer->transform($resource, $request)->willReturn($response);

        $sut = new TransformableResource($resource, $request, $transformer->reveal());

        self::assertSame($response, $sut->getContent());
    }

    #[Test]
    public function is_value_object(): void
    {
        $resource = new \stdClass();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $transformer = $this->prophesize(Transformer::class)->reveal();

        $sut = new TransformableResource($resource, $request, $transformer);

        self::assertSame($resource, $sut->resource);
        self::assertSame($request, $sut->request);
        self::assertSame($transformer, $sut->transformer);
    }
}
