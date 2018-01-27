<?php

require __DIR__ . '/vendor/autoload.php';

$passCases = [
    // spec
    'import defaultExport from "module-name";' => ['defaultExport'],
    'import * as name from "module-name";' => ['name'],
    'import { export } from "module-name";' => ['export'],
    'import { export as alias } from "module-name";' => ['alias'],
    'import { export1 , export2 } from "module-name";' => ['export1', 'export2'],
    'import { export1 , export2 as alias2} from "module-name";' => ['export1', 'alias2'],
    'import defaultExport, { export } from "module-name";' => ['defaultExport', 'export'],
    'import defaultExport, * as name from "module-name";' => ['defaultExport', 'name'],

    // extra
    'import defaultExport, * as jesus, {blah1, blah2, blah3 as yourMum} from "module-name";' => ['defaultExport', 'jesus', 'blah1', 'blah2', 'yourMum'],
];

$failCases = [
];

$parser = new \App\ImportStatementParser();

foreach ($passCases as $code => $expecting) {
    $parser->reset($code);
    $answer = $parser->getImportIdentifiers();

    $isExpected = empty(array_diff($answer, $expecting)) && empty(array_diff($expecting, $answer));

    if (!$isExpected) {
        echo "Failed [$code]\n";
        echo "Expecting: ", print_r($expecting, true), "\n";
        echo "Got: ", print_r($answer, true), "\n\n";
    }
}
