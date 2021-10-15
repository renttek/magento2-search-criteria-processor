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
            $filterProcessor ?? new FilterProcessor(),
            $limitProcessor ?? new LimitProcessor(),
            $sortProcessor ?? new SortOrderProcessor(),
        ];

        if ($joinProcessor !== null) {
            $processors[] = $joinProcessor;
        }

        parent::__construct($processors);
    }
}
