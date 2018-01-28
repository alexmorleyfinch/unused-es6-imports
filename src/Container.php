<?php

namespace Almofi\UnusedEs6Imports;

use Almofi\UnusedEs6Imports\App;
use Almofi\UnusedEs6Imports\Utils;
use Almofi\UnusedEs6Imports\Parser;
use Almofi\UnusedEs6Imports\Models;

class Container
{
    public function getUnusedImportRunner($rootDir) {
        return new App\UnusedImportRunner(
            $rootDir,
            new App\UnusedImportGenerator(
                new Parser\ImportStatementParser(
                    new Parser\Tokeniser(['import', 'from', 'as', '*', '{', '}', ','])
                ),
                new Utils\FilenameGenerator('/\.jsx?$/', true),
                new Models\StatementFactory()
            )
        );
    }
}
