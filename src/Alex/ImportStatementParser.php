<?php

namespace Alex;

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

        $this->expect('import', $token);

        // don't judge a lazy man okay, this one teeny tiny goto won't hurt anybody
        label:

        $token = $this->tokeniser->nextToken();

        if ($token->type == Token::T_IDENT) {
            $this->importStatement->setDefaultImport($token->value);
        } else if ($token->type == Token::T_OPEN) {
            $this->parseNamedImports($this->tokeniser);

            // by now we have consumed the close tag '}'
        } else if ($token->type === Token::T_STAR) {
            $this->expectKeyword('as', $this->tokeniser->nextToken());

            $aliasToken = $this->tokeniser->nextToken();
            $this->expectType(Token::T_IDENT, $aliasToken);
            $this->importStatement->setAllAlias($aliasToken->value);
        } else {
            throw new ParseError('Expected an identifier, an alias or a grouping');
        }

        $token = $this->tokeniser->nextToken();

        // could have comma or 'from'
        if ($token->type === Token::T_COMMA) {
            // do this shit again bitch!

            goto label; // just ignore this for now *cough cough*
        } else if ($token->isKeyword('from')) {
            // an end is in sight!!!
            // right now we know the tokeniser is shit and classifies the "module-name"; as a T_REF, and that's okay
            $token = $this->tokeniser->nextToken();

            $this->importStatement->setDependancyName($token->value);
        } else {
            throw new ParseError('Expected a comma or the "from" keyword');
        }
    }

    private function parseNamedImports($tokeniser)
    {
        $token = $this->tokeniser->nextToken();
        $this->expectType(Token::T_IDENT, $token);
        $name = $token->value; // remember, could be an `export` or `alias`

        // we need to check the next token before we can classify the T_IDENT we have
        $token = $this->tokeniser->nextToken();

        if ($token->isKeyword('as')) {
            $aliasToken = $this->tokeniser->nextToken();

            $this->expectType(Token::T_IDENT, $aliasToken);

            $this->importStatement->addNamedImport($name, $aliasToken->value);

            // set up next token after alias
            $token = $this->tokeniser->nextToken();
        } else {
            $this->importStatement->addNamedImport($name);
        }

        if ($token->type === Token::T_COMMA) {
            return $this->parseNamedImports($tokeniser);
        } else if ($token->type == Token::T_CLOSE) {
            return null;
        } else {
            throw new ParseError('Expecting a comma, a closing brace or the "as" keyword');
        }
    }
}
