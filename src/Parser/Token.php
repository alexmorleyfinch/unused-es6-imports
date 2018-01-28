<?php

namespace Almofi\UnusedEs6Imports\Parser;

class Token
{
    const T_CONTROL = 1;
    const T_LITERAL = 2;
    const T_IDENTIFIER = 3;

    /**
     * One of the const above
     *
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    /**
     * Token constructor.
     * @param string $value
     * @param int $type
     */
    public function __construct(string $value, int $type)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function equals(Token $token): bool
    {
        return $this->type == $token->type && $this->value == $token->value;
    }

    /**
     * @param int $type
     * @return bool
     */
    public function equalsType(int $type): bool
    {
        return $this->type == $type;
    }

    /**
     * @param string $word
     * @return bool
     */
    public function isControl(string $word): bool
    {
        return $this->value === $word && $this->type === self::T_CONTROL;
    }
}
