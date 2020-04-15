<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit\FieldExtractor;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Renttek\SearchCriteriaProcessor\FieldExtractor\SortOrderFieldExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SortOrderFieldExtractorTest extends TestCase
{
    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var SortOrder[]|MockObject[]
     */
    private $sortOrderMocks;

    /**
     * @var SortOrderFieldExtractor
     */
    private $sortOrderFieldExtractor;

    protected function setUp(): void
    {
        $this->sortOrderMocks = [
            $this->getSortOrderMock('table-1.field-1'),
            $this->getSortOrderMock('table-2.field-2'),
        ];

        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->getMock();

        $this->searchCriteriaMock
            ->method('getSortOrders')
            ->willReturn($this->sortOrderMocks);

        $this->sortOrderFieldExtractor = new SortOrderFieldExtractor;
    }

    public function testGetsSortOrdersFromSearchCriteria(): void
    {
        $this->searchCriteriaMock
            ->expects(self::once())
            ->method('getSortOrders');

        $this->sortOrderFieldExtractor->getFields($this->searchCriteriaMock);
    }

    public function testGetsFieldsFromAllSortOrders(): void
    {
        $this->sortOrderMocks[0]->expects(self::once())->method('getField');
        $this->sortOrderMocks[1]->expects(self::once())->method('getField');

        $this->sortOrderFieldExtractor->getFields($this->searchCriteriaMock);
    }

    public function testReturnsTablesOfAllSortOrders(): void
    {
        $expected = [['table-1', 'field-1'], ['table-2', 'field-2']];
        $actual   = $this->sortOrderFieldExtractor->getFields($this->searchCriteriaMock);

        self::assertEquals($expected, $actual);
    }

    /**
     * @return SortOrder|MockObject
     */
    private function getSortOrderMock(string $field): MockObject
    {
        $sortOrderMock = $this->getMockBuilder(SortOrder::class)->getMock();
        $sortOrderMock->method('getField')->willReturn($field);

        return $sortOrderMock;
    }
}
