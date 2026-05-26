<?php

namespace Src\Modules\Shared\Application\UseCase;

final class UseCaseResult
{
    private function __construct(
        private readonly bool    $success,
        private readonly ?string $message = null,
        private readonly mixed   $data    = null,
        private readonly string  $errorCode = ''
    ) {}

    public static function success(mixed $data = null): self
    {
        return new self(true, null, $data);
    }

    public static function failure(string $message, string $errorCode = ''): self
    {
        return new self(false, $message, null, $errorCode);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
    public function isFailure(): bool
    {
        return !$this->success;
    }
    public function getMessage(): string
    {
        return $this->message ?? '';
    }
    public function getData(): mixed
    {
        return $this->data;
    }
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
