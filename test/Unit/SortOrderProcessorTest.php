<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\DB\Select;
use Renttek\SearchCriteriaProcessor\SortOrderProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SortOrderProcessorTest extends TestCase
{
    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var Select|MockObject
     */
    private $selectMock;

    /**
     * @var SortOrderProcessor
     */
    private $sortOrderProcessor;

    /**
     * @var SortOrder[]|MockObject[]
     */
    private $sortOrders;

    protected function setUp(): void
    {
        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->getMock();

        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->selectMock
            ->method('order')
            ->willReturn($this->selectMock);

        $this->sortOrders = [
            $this->getSortOrderMock('field-1', 'ASC'),
            $this->getSortOrderMock('field-2', 'DESC'),
        ];

        $this->searchCriteriaMock
            ->method('getSortOrders')
            ->willReturn($this->sortOrders);

        $this->sortOrderProcessor = new SortOrderProcessor;
    }

    public function testReadsSortOrdersFromSearchCriteria(): void
    {
        $this->searchCriteriaMock
            ->expects(self::once())
            ->method('getSortOrders');

        $this->sortOrderProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testJoinsSortFieldsAndDirectionWithSpaceAndPassesToSelect(): void
    {
        $this->selectMock
            ->expects(self::once())
            ->method('order')
            ->with(['field-1 ASC', 'field-2 DESC']);

        $this->sortOrderProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    /**
     * @return SortOrder|MockObject
     */
    private function getSortOrderMock(string $field, string $direction): MockObject
    {
        $mock = $this->getMockBuilder(SortOrder::class)->getMock();
        $mock->method('getField')->willReturn($field);
        $mock->method('getDirection')->willReturn($direction);

        return $mock;
    }
}
