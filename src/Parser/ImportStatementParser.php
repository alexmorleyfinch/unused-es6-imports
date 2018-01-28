<?php

namespace Almofi\UnusedEs6Imports\Parser;

class ImportStatementParser
{
    /**
     * @var Tokeniser
     */
    protected $tokeniser;

    /**
     * @var Es6ImportInterface
     */
    private $importStatement;

    /**
     * @param Tokeniser $tokeniser
     */
    public function __construct(Tokeniser $tokeniser)
    {
        $this->tokeniser = $tokeniser;
    }

    // called from parent when `reset`
    public function parse(string $code, Es6ImportInterface $importStatement)
    {
        $this->tokeniser->reset($code);

        $this->importStatement = $importStatement;

        $token = $this->tokeniser->nextToken();

        $this->expectControl('import', $token);

        $this->recursiveOuterComma();
    }

    // keep accepting tokens and commas until the 'from' keyword
    private function recursiveOuterComma() {
        $token = $this->tokeniser->nextToken();

        if ($token->equalsType(Token::T_IDENTIFIER)) {
            $this->importStatement->setDefaultImport($token->getValue());
        } else if ($token->isControl('{')) {
            $this->recursiveInnerComma($this->tokeniser);

            // by now we have consumed the close tag '}'
        } else if ($token->isControl('*')) {
            $this->expectControl('as', $this->tokeniser->nextToken());

            $aliasToken = $this->tokeniser->nextToken();
            $this->expectType(Token::T_IDENTIFIER, $aliasToken);
            $this->importStatement->setAllAlias($aliasToken->getValue());
        } else {
            throw new ParseError('Expected an identifier, an alias or a grouping');
        }

        $token = $this->tokeniser->nextToken();

        // could have comma or 'from'
        if ($token->isControl(',')) {
            // do this shit again bitch!

            $this->recursiveOuterComma();
        } else if ($token->isControl('from')) {
            // an end is in sight!!!
            // right now we know the tokeniser is shit and classifies the "module-name"; as a T_REF, and that's okay
            $token = $this->tokeniser->nextToken();

            $this->importStatement->setDependancyName($token->getValue());
        } else {
            throw new ParseError('Expected a comma or the "from" keyword');
        }
    }

    // keep accepting tokens and commas until the '}' keyword
    private function recursiveInnerComma($tokeniser)
    {
        $token = $this->tokeniser->nextToken();
        $this->expectType(Token::T_IDENTIFIER, $token);
        $name = $token->getValue(); // remember, could be an `export` or `alias`

        // we need to check the next token before we can classify the T_IDENT we have
        $token = $this->tokeniser->nextToken();

        if ($token->isControl('as')) {
            $aliasToken = $this->tokeniser->nextToken();

            $this->expectType(Token::T_IDENTIFIER, $aliasToken);

            $this->importStatement->addNamedImport($name, $aliasToken->getValue());

            // set up next token after alias
            $token = $this->tokeniser->nextToken();
        } else {
            $this->importStatement->addNamedImport($name);
        }

        if ($token->isControl(',')) {
            return $this->recursiveInnerComma($tokeniser);
        } else if ($token->isControl('}')) {
            return null;
        } else {
            throw new ParseError('Expecting a comma, a closing brace or the "as" keyword');
        }
    }

    private function expectType(int $type, Token $token)
    {
        if (!$token->equalsType($type)) {
            throw new ParseError("Expecting type $type");
        }
    }

    private function expectControl(string $word, Token $token)
    {
        if (!$token->isControl($word)) {
            throw new ParseError("Expecting keyword $word");
        }
    }
}
