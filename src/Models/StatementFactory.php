<?php

namespace Almofi\UnusedEs6Imports\Models;

class StatementFactory
{
    public function importStatement() {
        return new ImportStatement();
    }
}
