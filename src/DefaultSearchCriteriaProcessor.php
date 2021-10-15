<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor;

/**
 * @codeCoverageIgnore
 */
class DefaultSearchCriteriaProcessor extends ChainProcessor
{
    public function __construct(
        FilterProcessor $filterProcessor,
        LimitProcessor $limitProcessor,
        SortOrderProcessor $sortProcessor,
        JoinProcessor $joinProcessor = null
    ) {
        $processors = [
            $filterProcessor,
            $limitProcessor,
            $sortProcessor,
            $joinProcessor ?? new NullJoinProcessor()
        ];

        parent::__construct($processors);
    }
}
