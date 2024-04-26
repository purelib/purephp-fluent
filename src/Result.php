<?php
namespace purephp\fluent;

use purephp\fluent\contract\Result as ContractResult;
use purephp\functional\monad\contract\Either;

class Result implements ContractResult
{
    /**
     * @var Either
     */
    protected $result;

    public function __construct(Either $value)
    {
        $this->result = $value;
    }

    public static function ok($value = null): ContractResult
    {
        return new static(Either::of($value));
    }

    public static function err($err): ContractResult
    {
        return new static(Either::left($err));
    }

    public function isSuccessful(): bool
    {
        return $this->result->isRight();
    }

    public function getMessage(): string
    {
        if ($this->result->isRight()) {
            return '';
        }

        $leftValue = $this->result->getLeft();

        if (is_scalar($leftValue)) {
            return strval($leftValue);
        }

        if (is_array($leftValue)) {
            return json_encode($leftValue);
        } else if (is_object($leftValue)) {
            if (method_exists($leftValue, '__toString')) {
                return $leftValue->__toString();
            } else if (method_exists($leftValue, 'getMessage')) {
                return strval($leftValue->getMessage());
            } else {
                return json_encode($leftValue);
            }
        } else { //其他未处理类型
            return '';
        }
    }

    /**
     * 获得结果值
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        $rightValue = $this->result->getRight();

        if ($this->result->isRight() !== true) {
            throw new \RuntimeException($this->getMessage());
        }

        return $rightValue;
    }

    public function andThen(callable $onSuccess, callable $onError = null)
    {
        if ($this->result->isRight()) {
            return $onSuccess($this->result->getRight());
        } elseif ($onError !== null && $this->result->isLeft()) {
            return $onError($this->result->getLeft());
        }
        return $this;
    }

}
