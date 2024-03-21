<?php

declare(strict_types=1);

namespace PhoneBurnerTest\Api\Handler;

use PhoneBurner\Api\Handler\ReadHandler;
use PhoneBurner\Api\Handler\Resolver;
use PhoneBurner\Api\Handler\ResponseFactory;
use PhoneBurner\Api\Handler\TransformableResource;
use PhoneBurner\Api\Handler\Transformer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

class ReadHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<ResponseFactory>
     */
    private ObjectProphecy $factory;

    /**
     * @var ObjectProphecy<Resolver>
     */
    private ObjectProphecy $resolver;

    /**
     * @var ObjectProphecy<Transformer>
     */
    private ObjectProphecy $transformer;

    private ReadHandler $sut;

    protected function setUp(): void
    {
        $this->resolver = $this->prophesize(Resolver::class);
        $this->transformer = $this->prophesize(Transformer::class);

        $this->sut = new ReadHandler(
            $this->resolver->reveal(),
            $this->transformer->reveal(),
        );

        $this->factory = $this->prophesize(ResponseFactory::class);
    }

    #[Test]
    public function handle_resolves_resource_and_returns_resource(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $resource = new stdClass();
        $this->resolver->resolve($request)->willReturn($resource);

        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $this->factory->make(new TransformableResource($resource, $request, $this->transformer->reveal()), 200)
            ->willReturn($response);

        $this->sut->setResponseFactory($this->factory->reveal());

        self::assertSame($response, $this->sut->handle($request));
    }
}
