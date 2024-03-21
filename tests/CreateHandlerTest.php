<?php

declare(strict_types=1);

namespace PhoneBurnerTest\Api\Handler;

use PhoneBurner\Api\Handler\CreateHandler;
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

class CreateHandlerTest extends TestCase
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

    private CreateHandler $sut;

    protected function setUp(): void
    {
        $this->resolver = $this->prophesize(Resolver::class);
        $this->hydrator = $this->prophesize(Hydrator::class);
        $this->transformer = $this->prophesize(Transformer::class);

        $this->sut = new CreateHandler(
            $this->hydrator->reveal(),
            $this->transformer->reveal(),
        );

        $this->factory = $this->prophesize(ResponseFactory::class);
    }

    #[Test]
    public function handle_resolves_resource_and_returns_updated_resource(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $created = new stdClass();
        $this->hydrator->create($request)->willReturn($created);

        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $transformable_resource = new TransformableResource($created, $request, $this->transformer->reveal());
        $this->factory->make($transformable_resource, 201)
            ->willReturn($response);

        $this->sut->setResponseFactory($this->factory->reveal());

        self::assertSame($response, $this->sut->handle($request));
    }
}
