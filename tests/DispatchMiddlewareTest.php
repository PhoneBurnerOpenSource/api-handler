<?php

declare(strict_types=1);

namespace PhoneBurnerTest\Api\Handler;

use PhoneBurner\Api\Handler\DispatchMiddleware as SUT;
use PhoneBurner\Api\Handler\HandlerFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatchMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<HandlerFactory>
     */
    private ObjectProphecy $factory;

    private SUT $sut;

    protected function setUp(): void
    {
        $this->factory = $this->prophesize(HandlerFactory::class);
        $this->sut = new SUT($this->factory->reveal());
    }

    #[Test]
    public function process_passes_if_factory_cannot_handle(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $this->factory->canHandle($request->reveal())->willReturn(false);

        $next = $this->prophesize(RequestHandlerInterface::class);
        $response = $this->prophesize(ResponseInterface::class);

        $next->handle($request->reveal())->willReturn($response->reveal());

        self::assertSame($response->reveal(), $this->sut->process($request->reveal(), $next->reveal()));
    }

    #[Test]
    public function process_creates_handler_and_calls(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $this->factory->canHandle($request->reveal())->willReturn(true);

        $handler = $this->prophesize(RequestHandlerInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $handler->handle($request->reveal())->willReturn($response->reveal());

        $this->factory->makeForRequest($request->reveal())->willReturn($handler->reveal());

        $next = $this->prophesize(RequestHandlerInterface::class);

        self::assertSame($response->reveal(), $this->sut->process($request->reveal(), $next->reveal()));
    }
}
