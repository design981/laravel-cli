<?php

namespace Design\LaravelCli\Cli\Adapter;

use App\Http\Kernel;
use Design\LaravelCli\Contracts\RequestContracts;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

class WorkermanAdapter implements RequestContracts
{
    protected TcpConnection $conn;

    protected Request $request;

    public function __construct(TcpConnection $conn, Request $request)
    {
        $this->conn = $conn;
        $this->request = $request;

        $this->request();
    }

    /**
     * build request
     */
    public function request()
    {
        $request = $this->generageRequest();

        /**
         * @var $kernel Kernel
         */
        $kernel = app()->get(\Illuminate\Contracts\Http\Kernel::class);

        $laravelResponse = $kernel->handle(
            $request = \Illuminate\Http\Request::createFromBase($request)
        );

        $kernel->terminate($request, $laravelResponse);

        $this->response($laravelResponse);
    }

    /**
     * send
     *
     * @param Response $response
     */
    public function response(Response $response)
    {
        $this->conn->send(new \Workerman\Protocols\Http\Response(200, $response->headers->all(), $response->getContent()));

        Event::dispatch('workerman.response', [$this->conn, $this->request]);
    }

    /**
     * generate symfony request
     *
     * @return \Illuminate\Http\Request
     */
    private function generageRequest(): \Illuminate\Http\Request
    {

        $request = \Illuminate\Http\Request::create(
            $this->request->uri(),
            $this->request->method(),
            $this->request->post(),
            $this->request->cookie(),
            $this->request->file(),
            array_merge([
                'USER' => $this->conn->worker->user,
                'HOME' => $_SERVER['HOME'],
                'SERVER_NAME' => $this->request->host(),
                'SERVER_PORT' => $this->conn->getLocalPort(),
                'SERVER_ADDR' => $_SERVER['SERVER_IP'],
                'REMOTE_PORT' => $this->conn->getRemotePort(),
                'REMOTE_ADDR' => $this->conn->getRemoteIp(),
                'SERVER_PROTOCOL' => $this->request->protocolVersion()
            ], $this->getServerHeader($this->request->header()))
        );

        if (0 === strpos($request->headers->get('CONTENT_TYPE', ''), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), ['PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }

        return $request;
    }

    /**
     * @param $data
     * @return array
     */
    private function getServerHeader($data): array
    {
        $server = [];

        foreach ($data as $key => $val) {
            $server['HTTP_' . strtoupper(str_replace('-', '_', $key))] = $val;

            $server[strtoupper(str_replace('-', '_', $key))] = $val;
        }

        return $server;
    }
}
