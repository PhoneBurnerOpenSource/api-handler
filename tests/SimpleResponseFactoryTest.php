<?php

namespace PhoneBurnerTest\Api\Handler;

use PhoneBurner\Api\Handler\SimpleResponseFactory;
use PhoneBurner\Api\Handler\TransformableResource;
use PhoneBurner\Api\Handler\Transformer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class SimpleResponseFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<ResponseFactoryInterface>
     */
    private ObjectProphecy $response_factory;

    /**
     * @var ObjectProphecy<StreamFactoryInterface>
     */
    private ObjectProphecy $stream_factory;
    private SimpleResponseFactory $sut;

    protected function setUp(): void
    {
        $this->response_factory = $this->prophesize(ResponseFactoryInterface::class);
        $this->stream_factory = $this->prophesize(StreamFactoryInterface::class);

        $this->sut = new SimpleResponseFactory(
            $this->response_factory->reveal(),
            $this->stream_factory->reveal(),
        );
    }

    /**
     * @test
     * @testWith [200]
     *           [201]
     *           [202]
     */
    public function make_allows_varied_status_codes(int $status): void
    {
        $resource = new \stdClass();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $transformer = $this->prophesize(Transformer::class);

        $transformer->transform($resource, $request)->willReturn("content");

        $response = $this->prophesize(ResponseInterface::class);
        $this->response_factory->createResponse($status)->willReturn($response->reveal());

        $stream = $this->prophesize(StreamInterface::class);
        $this->stream_factory->createStream("content")->willReturn($stream->reveal());

        $with_response = $this->prophesize(ResponseInterface::class)->reveal();
        $response->withBody($stream->reveal())->willReturn($with_response);

        $transformable_resource = new TransformableResource($resource, $request, $transformer->reveal());

        self::assertSame(
            $with_response,
            $this->sut->make($transformable_resource, $status),
        );
    }

    /**
     * @test
     * @testWith [200]
     *           [201]
     *           [202]
     */
    public function make_allows_null_TransformableResource(int $status): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $this->response_factory->createResponse($status)->willReturn($response->reveal());

        self::assertSame(
            $response->reveal(),
            $this->sut->make(null, $status),
        );
    }

    /**
     * @test
     * @dataProvider provideReturns
     */
    public function make_returns_response_with_expected_body(mixed $value, string $content): void
    {
        $resource = new \stdClass();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $transformer = $this->prophesize(Transformer::class);

        $transformer->transform($resource, $request)->willReturn($value);

        $response = $this->prophesize(ResponseInterface::class);
        $this->response_factory->createResponse(200)->willReturn($response->reveal());

        $stream = $this->prophesize(StreamInterface::class);
        $this->stream_factory->createStream($content)->willReturn($stream->reveal());

        $with_response = $this->prophesize(ResponseInterface::class)->reveal();
        $response->withBody($stream->reveal())->willReturn($with_response);

        $transformable_resource = new TransformableResource($resource, $request, $transformer->reveal());

        self::assertSame(
            $with_response,
            $this->sut->make($transformable_resource, 200),
        );
    }

    /**
     * @test
     */
    public function make_allows_null(): void
    {
        $resource = new \stdClass();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $transformer = $this->prophesize(Transformer::class);

        $transformer->transform($resource, $request)->willReturn(null);

        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $this->response_factory->createResponse(200)->willReturn($response);

        $transformable_resource = new TransformableResource($resource, $request, $transformer->reveal());

        self::assertSame(
            $response,
            $this->sut->make($transformable_resource, 200),
        );
    }

    /**
     * @test
     */
    public function make_allows_resource(): void
    {
        $resource = new \stdClass();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $transformer = $this->prophesize(Transformer::class);

        $stream_resource = \fopen('php://temp', 'r+');

        $transformer->transform($resource, $request)->willReturn($stream_resource);

        $response = $this->prophesize(ResponseInterface::class);
        $this->response_factory->createResponse(200)->willReturn($response->reveal());

        $stream = $this->prophesize(StreamInterface::class);
        $this->stream_factory->createStreamFromResource($stream_resource)->willReturn($stream->reveal());

        $with_response = $this->prophesize(ResponseInterface::class)->reveal();
        $response->withBody($stream->reveal())->willReturn($with_response);

        $transformable_resource = new TransformableResource($resource, $request, $transformer->reveal());

        self::assertSame(
            $with_response,
            $this->sut->make($transformable_resource, 200),
        );
    }

    public function provideReturns(): \Generator
    {
        yield 'string' => ['an api response', 'an api response'];

        foreach ([true, false] as $value) {
            yield 'bool: ' . ($value ? 'true' : 'false') => [$value, \json_encode($value)];
        }

        $array = ['an' => 'api', 'response' => 'array'];
        yield 'array' => [$array, \json_encode($array)];

        $object = new \stdClass();
        $object->an = 'api';
        $object->response = 'object';

        yield 'object' => [$object, \json_encode($object)];
    }
}
