<?php
// Exception/Application/ApplicationExceptionInterface.php

namespace Src\Exception\Application;

use Throwable;

/**
 * @see ApplicationException rate limit, etc.
 */
interface ApplicationExceptionInterface extends Throwable
{
    public function getMessage(): string;
    public function getErrorCode(): string;
}
