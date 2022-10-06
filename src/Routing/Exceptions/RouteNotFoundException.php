<?php

namespace Src\Routing\Exceptions;

class RouteNotFoundException extends \Exception
{
    protected $message = '404 Not Found';
}