<?php

namespace purephp\fluent\contract;

interface Result
{
    public static function ok($value = null): Result;

    public static function err($err): Result;

    public function isSuccessful(): bool;

    public function getMessage(): string;

    public function getValue();

    public function andThen(callable $onSuccess, callable $onError = null);
}
