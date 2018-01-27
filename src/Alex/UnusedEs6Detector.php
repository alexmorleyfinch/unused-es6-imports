<?php

namespace Alex;


class UnusedEs6Detector
{
    public function matchImportStatements($es6source)
    {
        $matchCount = preg_match_all('/^\s*(import[\sA-Za-z0-9_\{\,\}]+from.+;)\s*$/m', $es6source, $matches);

        if ($matchCount === false) {
            trigger_error('bad regex');
            return null;
        }

        if ($matchCount < 0) {
            return null;
        }

        return $matches[0];
    }

    public function getImportIdentifiers($importStatements)
    {
        $importNames = [];
        $parser = new \Alex\ImportStatementParser();

        foreach ($importStatements as $idx => $importCode) {
            $parser->reset($importCode);

            $importNames = array_merge($importNames, $parser->getImportIdentifiers());
        }

        return $importNames;
    }

    public function getUnusedIdentifiers($es6source, $importNames)
    {
        $unusedImports = [];
        foreach ($importNames as $importName) {
            $matchCount = preg_match_all('/\\b' . preg_quote($importName) . '\\b/', $es6source);

            if ($matchCount === false) {
                trigger_error('bad regex');
                continue;
            }

            if ($matchCount === 1) {
                $unusedImports [] = $importName;
            }
        }

        return $unusedImports;
    }
}