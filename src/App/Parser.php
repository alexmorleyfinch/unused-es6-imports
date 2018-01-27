<?php

namespace Almofi\UnusedEs6Imports\App;

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

    protected function expectType(int $type, Token $token)
    {
        if (!$token->equalsType($type)) {
            throw new ParseError("Expecting type $type");
        }
    }

    protected function expectControl(string $word, Token $token)
    {
        if (!$token->isControl($word)) {
            throw new ParseError("Expecting keyword $word");
        }
    }
}