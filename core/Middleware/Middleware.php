<?php

namespace Core\Middleware;

abstract class Middleware
{
    abstract public function handle(): bool;
}
