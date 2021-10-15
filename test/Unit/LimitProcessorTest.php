<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;
use Renttek\SearchCriteriaProcessor\LimitProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LimitProcessorTest extends TestCase
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
     * @var LimitProcessor
     */
    private $limitProcessor;

    protected function setUp(): void
    {
        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->getMock();

        $this->selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->limitProcessor = new LimitProcessor();
    }

    public function testReturnsSelectUnchangedIfPageSizeIsNull(): void
    {
        $this->selectMock
            ->expects(self::never())
            ->method(self::anything());

        $this->limitProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testGetsPageSizeFromSearchCriteria(): void
    {
        $this->searchCriteriaMock
            ->expects(self::once())
            ->method('getPageSize');

        $this->limitProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testGetsCurrentPageFromSearchCriteria(): void
    {
        $this->searchCriteriaMock
            ->expects(self::once())
            ->method('getCurrentPage');

        $this->limitProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testPassesPageSizeAsCountToSelect(): void
    {
        $pageSize = 15;

        $this->searchCriteriaMock
            ->method('getPageSize')
            ->willReturn($pageSize);

        $this->selectMock
            ->expects(self::once())
            ->method('limit')
            ->with($pageSize, self::anything());

        $this->limitProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }

    public function testPassesPageSizeTimesCurrentPageAsOffsetToSelect(): void
    {
        $pageSize    = 13;
        $currentPage = 7;

        $this->searchCriteriaMock
            ->method('getPageSize')
            ->willReturn($pageSize);

        $this->searchCriteriaMock
            ->method('getCurrentPage')
            ->willReturn($currentPage);

        $this->selectMock
            ->expects(self::once())
            ->method('limit')
            ->with(self::anything(), 78); // (7 - 1) * 13

        $this->limitProcessor->process($this->selectMock, $this->searchCriteriaMock);
    }
}
