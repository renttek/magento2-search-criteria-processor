<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Integration;

require_once __DIR__ . '/bootstrap.php';

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteria;
use Renttek\SearchCriteriaProcessor\FilterProcessor;
use Renttek\SearchCriteriaProcessor\LimitProcessor;
use PHPUnit\Framework\TestCase;

class FilterProcessorTest extends TestCase
{
    private FilterProcessor $filterProcessor;

    protected function setUp(): void
    {
        $this->filterProcessor = new FilterProcessor();
    }

    public function testSingleFilter(): void
    {
        $filterGroup = new FilterGroup();
        $filterGroup->setFilters([(new Filter)->setField('bar')->setValue('baz')->setConditionType('eq')]);

        $searchCriteria = (new SearchCriteria)->setFilterGroups([$filterGroup]);
        $select         = getAdapter()->select()->from('foo');

        $this->filterProcessor->process($select, $searchCriteria);

        self::assertMatchesRegularExpression("/ WHERE \(bar = 'baz'\)$/", (string)$select);
    }

    public function testMultipleFilterSingleFilterGroup(): void
    {
        $filters = [
            (new Filter)->setField('bar')->setValue('baz')->setConditionType('eq'),
            (new Filter)->setField('lorem')->setValue('ipsum')->setConditionType('neq')
        ];

        $filterGroup = new FilterGroup();
        $filterGroup->setFilters($filters);

        $searchCriteria = (new SearchCriteria)->setFilterGroups([$filterGroup]);
        $select         = getAdapter()->select()->from('foo');

        $this->filterProcessor->process($select, $searchCriteria);

        self::assertMatchesRegularExpression("/ WHERE \(bar = 'baz' OR lorem != 'ipsum'\)$/", (string)$select);
    }

    public function testMultipleFilterMultipleFilterGroups(): void
    {
        $filters1 = [
            (new Filter)->setField('bar')->setValue('baz')->setConditionType('eq'),
            (new Filter)->setField('lorem')->setValue('ipsum')->setConditionType('neq')
        ];

        $filterGroup1 = new FilterGroup();
        $filterGroup1->setFilters($filters1);

        $filters2 = [
            (new Filter)->setField('chuck')->setValue('norris')->setConditionType('neq'),
            (new Filter)->setField('test')->setValue('test')->setConditionType('eq')
        ];

        $filterGroup1 = new FilterGroup();
        $filterGroup1->setFilters($filters1);

        $filterGroup2 = new FilterGroup();
        $filterGroup2->setFilters($filters2);

        $searchCriteria = (new SearchCriteria)->setFilterGroups([$filterGroup1, $filterGroup2]);
        $select         = getAdapter()->select()->from('foo');

        $this->filterProcessor->process($select, $searchCriteria);

        $sql = (string)$select;
        self::assertStringContainsString("(bar = 'baz' OR lorem != 'ipsum')", $sql);
        self::assertStringContainsString("(chuck != 'norris' OR test = 'test')", $sql);
        self::assertMatchesRegularExpression("/ WHERE \(.*?\) AND \(.*?\)$/", (string)$select);
    }
}
