<?php

namespace App;

abstract class Parser
{
    /**
     * @var Tokeniser
     */
    protected $tokeniser;

    public function __construct(Tokeniser $tokeniser)
    {
        $this->tokeniser = $tokeniser;
    }

    abstract protected function parse();

    public function reset($code)
    {
        $this->tokeniser->reset($code);

        $this->parse();
    }

    protected function expect(string $value, Token $token)
    {
        if ($value !== $token->value) {
            throw new ParseError("Expecting $value");
        }
    }

    protected function expectType(int $type, Token $token)
    {
        if ($type !== $token->type) {
            throw new ParseError("Expecting type $type");
        }
    }

    protected function expectKeyword(string $word, Token $token)
    {
        if (!$token->isKeyword($word)) {
            throw new ParseError("Expecting keyword $word");
        }
    }
}