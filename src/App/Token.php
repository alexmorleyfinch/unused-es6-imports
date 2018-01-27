<?php

namespace Almofi\UnusedEs6Imports\App;

class Token {
    const T_IDENT = 1;
    const T_KEYWORD = 2;
    const T_STR_LIT = 3;

    const T_OPEN = 4;
    const T_CLOSE = 5;
    const T_COMMA = 6;
    const T_STAR = 7;
    const T_CONTROL = 8;

    public $value;
    public $type;

    private static $map = [
        '{' => Token::T_OPEN,
        '}' => Token::T_CLOSE,
        ',' => Token::T_COMMA,
        '*' => Token::T_STAR,
    ];

    public function __construct(string $value, $type = Token::T_IDENT)
    {
        $this->value = $value;

        if ($type === Token::T_CONTROL) {
            $type = $this->classify($value);
        }
        $this->type = $type;
    }

    public function equals(Token $token) {
        return $this->type == $token->type && $this->value == $token->value;
    }

    public function isControl(string $word) {
        return $this->value === $word && $this->type === self::T_KEYWORD;
    }

    public function classify($value) {
        return self::$map[$value];
    }
}