<?php

namespace Almofi\UnusedEs6Imports\Parser;

// TODO detect string literal

class Tokeniser
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var string
     */
    private $source;

    /**
     * @var int
     */
    private $position;

    /**
     * @var array
     */
    private $controlChars;

    /**
     * @var array
     */
    private $controlWords;

    /**
     * @param array $controlStrings
     */
    public function __construct(array $controlStrings = []) {
        foreach ($controlStrings as $word) {
            $wordLen = strlen($word);
            if ($wordLen === 1) {
                $this->controlChars []= $word;
            } else if ($wordLen > 1) {
                $this->controlWords []= $word;
            }
        }
    }

    /**
     * @param string $importString
     */
    public function reset(string $importString)
    {
        $this->position = 0;
        $this->source = $importString;
    }

    /**
     * @return Token|null
     */
    public function nextToken(): ?Token
    {
        $this->skipSpaces();

        if (!isset($this->source[$this->position])) {
            return null;
        }

        // guaranteed not to be a space ^.^
        $nextChar = $this->source[$this->position];

        if (in_array($nextChar, $this->controlChars)) {
            $this->position++;
            return new Token($nextChar, Token::T_CONTROL);
        }

        return $this->buildString($nextChar);
    }

    /**
     * @param string $firstChr
     * @return Token
     */
    private function buildString(string $firstChr): Token
    {
        $this->string = $firstChr;
        $this->position++; // we already have the first character;

        while (isset($this->source[$this->position])) {
            $nextChar = $this->source[$this->position];

            if (ctype_space($nextChar) || in_array($nextChar, $this->controlChars)) {
                break;
            }

            $this->position++;
            $this->string .= $nextChar;
        }

        if (in_array($this->string, $this->controlWords)) {
            return new Token($this->string, Token::T_CONTROL);
        }

        return new Token($this->string, Token::T_IDENTIFIER);
    }

    private function skipSpaces()
    {
        while (ctype_space($this->source[$this->position] ?? null)) {
            $this->position++;
        }
    }
}
