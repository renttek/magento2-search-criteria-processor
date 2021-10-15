<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit;

use PHPUnit\Framework\TestCase;
use function Renttek\SearchCriteriaProcessor\groupFieldsByTables;

class FunctionsTest extends TestCase
{
    public function testGroupFieldsByTables(): void
    {
        $input    = [
            ['table-1', 'field-1-1'],
            ['table-2', 'field-2-1'],
            ['table-2', 'field-2-2'],
            ['table-1', 'field-1-2'],
            ['table-1', 'field-1-3'],
        ];
        $expected = [
            'table-1' => ['field-1-1', 'field-1-2', 'field-1-3'],
            'table-2' => ['field-2-1', 'field-2-2'],
        ];

        self::assertEquals($expected, groupFieldsByTables($input));
    }
}
