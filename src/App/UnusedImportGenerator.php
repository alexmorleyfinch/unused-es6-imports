<?php

namespace Almofi\UnusedEs6Imports\App;

use Almofi\UnusedEs6Imports\Utils;
use Almofi\UnusedEs6Imports\Models;
use Almofi\UnusedEs6Imports\Parser;

class UnusedImportGenerator
{
    /**
     * @var Parser\ImportStatementParser
     */
    private $parser;

    /**
     * @var Utils\FilenameGenerator
     */
    private $filenameGenerator;

    public function __construct()
    {
        $this->parser = new Parser\ImportStatementParser(
            new Parser\Tokeniser(['import', 'from', 'as', '*', '{', '}', ','])
        );
        $this->filenameGenerator = new Utils\FilenameGenerator('/\.jsx?$/', true);
    }

    /**
     * @param string $rootDir
     * @return \Generator
     */
    public function generateUnusedImportIdentifiers(string $rootDir): \Generator
    {
        $generator = $this->filenameGenerator->recurseFiles($rootDir);

        foreach ($generator as $filename) {
            $es6source = file_get_contents($filename);

            if ($es6source === false) {
                trigger_error("Could not get contents $filename");
                continue;
            }

            // TODO will eventually contain info about line numbers so we can remove the unused identifiers
            $importStatements = $this->matchImportStatements($es6source);

            if (empty($importStatements)) {
                continue;
            }

            $importNames = $this->getImportIdentifiers($importStatements);

            $unusedImports = $this->getUnusedIdentifiers($es6source, $importNames);

            if (empty($unusedImports)) {
                continue;
            }

            // TODO make this an object representation
            yield ['filename' => $filename, 'unusedIdentifiers' => $unusedImports];
        }
    }

    /**
     * @param string $es6source
     * @return array
     */
    private function matchImportStatements(string $es6source): array
    {
        $matchCount = preg_match_all('/^\s*(import[\sA-Za-z0-9_\{\,\}]+from.+;)\s*$/m', $es6source, $matches);

        if ($matchCount === false) {
            trigger_error('bad regex');
            return [];
        }

        if ($matchCount < 0) {
            return [];
        }

        return $matches[0];
    }

    /**
     * @param array $importStatements
     * @return array
     */
    private function getImportIdentifiers(array $importStatements): array
    {
        $importNames = [];

        foreach ($importStatements as $idx => $importCode) {
            $importStatement = new Models\ImportStatement();

            $this->parser->parse($importCode, $importStatement);

            $importNames = array_merge($importNames, $importStatement->getNamedImports());
        }

        return $importNames;
    }

    /**
     * @param string $es6source
     * @param array $importNames
     * @return array
     */
    private function getUnusedIdentifiers(string $es6source, array $importNames): array
    {
        $unusedImports = [];
        foreach ($importNames as $importName) {
            $matchCount = preg_match_all('/\\b' . preg_quote($importName) . '\\b/', $es6source);

            if ($matchCount === false) {
                trigger_error('bad regex');
                continue;
            }

            if ($matchCount === 1) {
                $unusedImports[] = $importName;
            }
        }

        return $unusedImports;
    }
}
