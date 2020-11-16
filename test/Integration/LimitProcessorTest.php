<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\Test\Integration;

require_once __DIR__ . '/bootstrap.php';

use Magento\Framework\Api\SearchCriteria;
use Renttek\SearchCriteriaProcessor\LimitProcessor;
use PHPUnit\Framework\TestCase;

class LimitProcessorTest extends TestCase
{
    private LimitProcessor $limitProcessor;

    protected function setUp(): void
    {
        $this->limitProcessor = new LimitProcessor;
    }

    public function testOnlyPageSize(): void
    {
        $searchCriteria = (new SearchCriteria)->setPageSize(13);
        $select         = getAdapter()->select()->from('foo');

        $this->limitProcessor->process($select, $searchCriteria);

        self::assertMatchesRegularExpression('/ LIMIT 13$/', (string)$select);
    }

    public function testOnlyCurrentPage(): void
    {
        $searchCriteria = (new SearchCriteria)->setCurrentPage(7);
        $select         = getAdapter()->select()->from('foo');

        $this->limitProcessor->process($select, $searchCriteria);

        self::assertStringNotContainsString('LIMIT', (string)$select);
    }

    public function testPageSizeAndCurrentPage(): void
    {
        $searchCriteria = (new SearchCriteria)->setPageSize(9)->setCurrentPage(7);
        $select         = getAdapter()->select()->from('foo');

        $this->limitProcessor->process($select, $searchCriteria);

        self::assertMatchesRegularExpression('/ LIMIT 9 OFFSET 63$/', (string)$select);
    }
}
