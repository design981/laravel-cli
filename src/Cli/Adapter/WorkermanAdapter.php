<?php

namespace Design\LaravelCli\Cli\Adapter;

use App\Http\Kernel;
use Design\LaravelCli\Contracts\RequestContracts;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

class WorkermanAdapter implements RequestContracts
{
    protected TcpConnection $conn;

    protected Request $request;

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

        return $laravelResponse;
    }

    public function response(Response $response)
    {
        $this->conn->send(new \Workerman\Protocols\Http\Response(200, $response->headers->all(), $response->getContent()));
    }

    /**
     * @return \Illuminate\Http\Request
     */
    private function generageRequest(): \Illuminate\Http\Request
    {
        $request = \Illuminate\Http\Request::create($this->request->uri(), $this->request->method(), [], $this->request->cookie(), $this->request->file(), array_merge([
            'USER' => $this->conn->worker->user,
            'HOME' => $_SERVER['HOME'],
            'SERVER_NAME' => $this->request->host(),
            'SERVER_PORT' => $this->conn->getLocalPort(),
            'SERVER_ADDR' => $this->conn->getLocalAddress(),
            'REMOTE_PORT' => $this->conn->getRemotePort(),
            'REMOTE_ADDR' => $this->conn->getRemoteIp(),
        ], $this->getServerHeader($this->request->header())));

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
        }

        return $server;
    }
}
