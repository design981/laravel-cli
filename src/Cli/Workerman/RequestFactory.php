<?php

namespace Design\LaravelCli\Cli\Workerman;

use Design\LaravelCli\Contracts\WorkermanRequestFactoryContracts;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Workerman\Connection\TcpConnection as WorkermanTcpConnection;
use Workerman\Protocols\Http\Request as WorkermanRequest;

class RequestFactory implements WorkermanRequestFactoryContracts
{

    /**
     * @param WorkermanTcpConnection $conn
     * @param WorkermanRequest $workermanRequest
     * @return Request
     */
    public function create(WorkermanTcpConnection $conn, WorkermanRequest $workermanRequest): Request
    {
        $request = Request::create(
            $workermanRequest->uri(),
            $workermanRequest->method(),
            $workermanRequest->post(),
            $workermanRequest->cookie(),
            $workermanRequest->file(),
            array_merge([
                'USER' => $conn->worker->user,
                'HOME' => $_SERVER['HOME'],
                'SERVER_NAME' => $workermanRequest->host(),
                'SERVER_PORT' => $conn->getLocalPort(),
                'SERVER_ADDR' => $_SERVER['SERVER_IP'],
                'REMOTE_PORT' => $conn->getRemotePort(),
                'REMOTE_ADDR' => $conn->getRemoteIp(),
                'SERVER_PROTOCOL' => $workermanRequest->protocolVersion()
            ], $this->getServerHeader($workermanRequest->header()))
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
        foreach ($data as $key => $val) {
            $server['HTTP_' . strtoupper(str_replace('-', '_', $key))] = $val;
            $server[strtoupper(str_replace('-', '_', $key))] = $val;
        }
        return $server ?? [];
    }
}
