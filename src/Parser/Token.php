<?php

namespace Almofi\UnusedEs6Imports\Parser;

class Token {
    const T_CONTROL = 1;
    const T_LITERAL = 2;
    const T_IDENTIFIER = 3;

    private $type;
    private $value;

    public function __construct(string $value, int $type)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function equals(Token $token) {
        return $this->type == $token->type && $this->value == $token->value;
    }

    public function equalsType(int $type) {
        return $this->type == $type;
    }

    public function isControl(string $word) {
        return $this->value === $word && $this->type === self::T_CONTROL;
    }
}