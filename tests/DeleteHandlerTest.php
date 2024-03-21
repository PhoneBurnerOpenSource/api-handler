<?php

declare(strict_types=1);

namespace PhoneBurnerTest\Api\Handler;

use PhoneBurner\Api\Handler\DeleteHandler;
use PhoneBurner\Api\Handler\Hydrator;
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

class DeleteHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<ResponseFactory>
     */
    private ObjectProphecy $factory;

    /**
     * @var ObjectProphecy<Hydrator>
     */
    private ObjectProphecy $hydrator;

    /**
     * @var ObjectProphecy<Resolver>
     */
    private ObjectProphecy $resolver;

    /**
     * @var ObjectProphecy<Transformer>
     */
    private ObjectProphecy $transformer;

    private DeleteHandler $sut;

    protected function setUp(): void
    {
        $this->resolver = $this->prophesize(Resolver::class);
        $this->hydrator = $this->prophesize(Hydrator::class);
        $this->transformer = $this->prophesize(Transformer::class);

        $this->sut = new DeleteHandler(
            $this->resolver->reveal(),
            $this->hydrator->reveal(),
            $this->transformer->reveal(),
        );

        $this->factory = $this->prophesize(ResponseFactory::class);
    }

    #[Test]
    public function handle_resolves_resource_and_returns_deleted_resource(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $resource = new stdClass();
        $this->resolver->resolve($request)->willReturn($resource);

        $deleted = new stdClass();
        $this->hydrator->delete($request, $resource)->willReturn($deleted);

        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $this->factory->make(new TransformableResource($deleted, $request, $this->transformer->reveal()), 200)
            ->willReturn($response);

        $this->sut->setResponseFactory($this->factory->reveal());

        self::assertSame($response, $this->sut->handle($request));
    }

    #[Test]
    public function handle_resolves_resource_and_returns_empty_response(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $resource = new stdClass();
        $this->resolver->resolve($request)->willReturn($resource);

        $this->hydrator->delete($request, $resource)->willReturn(null);

        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $this->factory->make(null, 204)
            ->willReturn($response);

        $this->sut->setResponseFactory($this->factory->reveal());

        self::assertSame($response, $this->sut->handle($request));
    }
}
