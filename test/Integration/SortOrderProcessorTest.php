<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Integration;

require_once __DIR__ . '/bootstrap.php';

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Renttek\SearchCriteriaProcessor\SortOrderProcessor;
use PHPUnit\Framework\TestCase;

class SortOrderProcessorTest extends TestCase
{
    /**
     * @var SortOrderProcessor
     */
    private $sortOrderProcessor;

    protected function setUp(): void
    {
        $this->sortOrderProcessor = new SortOrderProcessor;
    }

    public function testSingleSortOrder(): void
    {
        $sortOrder      = $this->getSortOrder('myField', 'ASC');
        $searchCriteria = (new SearchCriteria)->setSortOrders([$sortOrder]);
        $select         = getAdapter()->select()->from('foo');

        $this->sortOrderProcessor->process($select, $searchCriteria);

        self::assertRegExp('/ ORDER BY `myField` ASC$/', (string)$select);
    }

    public function testMultipleSortOrders(): void
    {
        $sortOrders = [
            $this->getSortOrder('first', 'DESC'),
            $this->getSortOrder('second', 'ASC'),
        ];

        $searchCriteria = (new SearchCriteria)->setSortOrders($sortOrders);
        $select         = getAdapter()->select()->from('foo');

        $this->sortOrderProcessor->process($select, $searchCriteria);

        self::assertRegExp('/ ORDER BY `first` DESC, `second` ASC$/', (string)$select);
    }

    private function getSortOrder(string $field, string $direction): SortOrder
    {
        return (new SortOrder)
            ->setField($field)
            ->setDirection($direction);
    }
}
