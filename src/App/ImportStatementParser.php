<?php

namespace Almofi\UnusedEs6Imports\App;

class ImportStatementParser extends Parser
{
    /**
     * @var ImportStatement
     */
    private $importStatement;

    public function __construct()
    {
        parent::__construct(new Tokeniser(['import', 'from', 'as', '*', '{', '}', ',']));
    }

    public function getImportIdentifiers(): array
    {
        return $this->importStatement->getNamedImports();
    }

    protected function parse()
    {
        $this->importStatement = new ImportStatement();

        $token = $this->tokeniser->nextToken();

        $this->expectControl('import', $token);

        // don't judge a lazy man okay, this one teeny tiny goto won't hurt anybody
        label:

        $token = $this->tokeniser->nextToken();

        if ($token->equalsType(Token::T_IDENTIFIER)) {
            $this->importStatement->setDefaultImport($token->getValue());
        } else if ($token->isControl('{')) {
            $this->parseNamedImports($this->tokeniser);

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

            goto label; // just ignore this for now *cough cough*
        } else if ($token->isControl('from')) {
            // an end is in sight!!!
            // right now we know the tokeniser is shit and classifies the "module-name"; as a T_REF, and that's okay
            $token = $this->tokeniser->nextToken();

            $this->importStatement->setDependancyName($token->getValue());
        } else {
            throw new ParseError('Expected a comma or the "from" keyword');
        }
    }

    private function parseNamedImports($tokeniser)
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
            return $this->parseNamedImports($tokeniser);
        } else if ($token->isControl('}')) {
            return null;
        } else {
            throw new ParseError('Expecting a comma, a closing brace or the "as" keyword');
        }
    }
}
