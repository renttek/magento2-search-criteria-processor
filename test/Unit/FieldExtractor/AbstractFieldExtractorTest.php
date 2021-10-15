<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit\FieldExtractor;

use Renttek\SearchCriteriaProcessor\FieldExtractor\AbstractFieldExtractor;
use PHPUnit\Framework\TestCase;

class AbstractFieldExtractorTest extends TestCase
{
    public function testReturnsNullIfValueHasNoPoint(): void
    {
        self::assertNull($this->getDummyFieldExtractor()->test('test'));
    }

    public function testReturnsNullIfValueHasMoreThanOnePoint(): void
    {
        self::assertNull($this->getDummyFieldExtractor()->test('test.test.test'));
    }

    public function testReturnsTheExplodedValueAsArray(): void
    {
        $expected = ['ab', 'cd'];
        $actual   = $this->getDummyFieldExtractor()->test('ab.cd');

        self::assertEquals($expected, $actual);
    }

    private function getDummyFieldExtractor()
    {
        return new class() extends AbstractFieldExtractor {
            public function test(string $value): ?array
            {
                return $this->getFieldTable($value);
            }
        };
    }
}
