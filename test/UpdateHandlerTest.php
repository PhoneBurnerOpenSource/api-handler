<?php
declare(strict_types=1);

namespace PhoneBurnerTest\Api\Handler;

use PhoneBurner\Api\Handler\Hydrator;
use PhoneBurner\Api\Handler\Resolver;
use PhoneBurner\Api\Handler\ResponseFactory;
use PhoneBurner\Api\Handler\TransformableResource;
use PhoneBurner\Api\Handler\Transformer;
use PhoneBurner\Api\Handler\UpdateHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

class UpdateHandlerTest extends TestCase
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

    private UpdateHandler $sut;
    protected function setUp(): void
    {
        $this->resolver = $this->prophesize(Resolver::class);
        $this->hydrator = $this->prophesize(Hydrator::class);
        $this->transformer = $this->prophesize(Transformer::class);

        $this->sut = new UpdateHandler(
            $this->resolver->reveal(),
            $this->hydrator->reveal(),
            $this->transformer->reveal(),
        );

        $this->factory = $this->prophesize(ResponseFactory::class);
    }

    /**
     * @test
     */
    public function handle_resolves_resource_and_returns_updated_resource(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $resource = new stdClass();
        $this->resolver->resolve($request)->willReturn($resource);

        $updated = new stdClass();
        $this->hydrator->update($request, $resource)->willReturn($updated);

        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $this->factory->make(new TransformableResource($updated, $request, $this->transformer->reveal()), 200)
            ->willReturn($response);

        $this->sut->setResponseFactory($this->factory->reveal());

        self::assertSame($response, $this->sut->handle($request));
    }
}