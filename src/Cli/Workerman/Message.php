<?php

namespace Design\LaravelCli\Cli\Workerman;

use App\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Workerman\Connection\TcpConnection as WorkermanTcpConnection;
use Workerman\Protocols\Http\Request as WorkermanRequest;
use Workerman\Worker;

class Message
{
    /** @var Kernel */
    protected Kernel $kernel;

    /** @var RequestFactory */
    protected RequestFactory $requestFactory;

    /** @var ResponseFactory */
    protected ResponseFactory $responseFactory;

    /**
     * Message constructor.
     * @param RequestFactory $requestFactory
     * @param ResponseFactory $responseFactory
     * @param Kernel $kernel
     */
    public function __construct(RequestFactory $requestFactory, ResponseFactory $responseFactory, Kernel $kernel)
    {
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->kernel = $kernel;
    }

    /**
     * @param WorkermanTcpConnection $conn
     * @param WorkermanRequest $workermanRequest
     */
    public function __invoke(WorkermanTcpConnection $conn, WorkermanRequest $workermanRequest)
    {
        $this->maxRequest();

        $laravelResponse = $this->kernel->handle(
            $request = Request::createFromBase($this->requestFactory->create($conn, $workermanRequest))
        );
        $this->kernel->terminate($request, $laravelResponse);

        $this->responseFactory->send($laravelResponse, $conn);
        Event::dispatch('workerman.response', [$conn, $workermanRequest]);
    }

    /**
     * max request restart
     */
    private function maxRequest(): void
    {
        static $requestCount = 0;
        if (++$requestCount >= 100000) {
            Worker::stopAll();
        }
    }
}
