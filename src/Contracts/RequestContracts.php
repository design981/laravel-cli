<?php
namespace Design\LaravelCli\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface RequestContracts
{
    public function request();

    public function response(Response $response);
}
