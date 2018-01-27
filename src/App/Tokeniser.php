<?php

namespace Almofi\UnusedEs6Imports\App;

class Tokeniser
{
    private $string;
    private $source;
    private $position;
    private $controlChars;
    private $controlWords;

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

    public function reset($importString)
    {
        $this->position = 0;
        $this->source = $importString;
    }

    public function nextToken()
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

    private function buildString($firstChr)
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
            return new Token($this->string, Token::T_KEYWORD);
        }

        return new Token($this->string);
    }

    private function skipSpaces()
    {
        while (ctype_space($this->source[$this->position] ?? null)) {
            $this->position++;
        }
    }
}