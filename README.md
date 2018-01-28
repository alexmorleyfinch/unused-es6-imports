# Unused ES6 Imports

This super fast package detects any unused [es6 style imports](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import)

### Basic usage

```bash
php unused-imports.php -f [filename] [-j]
```

This will output a filename followed by any unused imports per line to the console.

#### Sample raw output

```bash
/dev/myfile.2js > React
/dev/myfile1.js > PropTypes, immutable
 
Total number of files with unused imports: 2
Total number of unused imports from all files: 3
```

#### Arguments

Required:

`-f [filename]` The directory name to be recursed.

Optional:

`-j` If flagged, the script outputs JSON.

### Coming up
  - more CLI options
  - more control of CLI output
  - remove unused imports from source code

### Tests

There is a rudimentary test script `php test.php` which tests the parser against the es6 import specification. The tests will be improved upon
