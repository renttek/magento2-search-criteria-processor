<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Unit\FieldExtractor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Renttek\SearchCriteriaProcessor\FieldExtractor\ChainFieldExtractor;
use Renttek\SearchCriteriaProcessor\FieldExtractor\FieldExtractorInterface;
use Renttek\SearchCriteriaProcessor\FieldExtractor\FilterFieldExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ChainFieldExtractorTest extends TestCase
{
    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var FieldExtractorInterface|MockObject
     */
    private $fieldExtractorMock;

    /**
     * @var ChainFieldExtractor
     */
    private $chainFieldExtractor;

    protected function setUp(): void
    {
        $this->searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->getMock();

        $this->fieldExtractorMock = $this->getMockBuilder(FieldExtractorInterface::class)
            ->getMock();

        $this->chainFieldExtractor = new ChainFieldExtractor([$this->fieldExtractorMock]);
    }

    public function testCallsEveryExtractorWithSearchCriteria(): void
    {
        $this->fieldExtractorMock
            ->expects(self::once())
            ->method('getFields')
            ->with($this->searchCriteriaMock);

        $this->chainFieldExtractor->getFields($this->searchCriteriaMock);
    }
}
