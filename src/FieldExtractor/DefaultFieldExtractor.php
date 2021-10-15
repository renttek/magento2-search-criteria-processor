<?php

declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

/**
 * @codeCoverageIgnore
 */
class DefaultFieldExtractor extends ChainFieldExtractor
{
    public function __construct(array $fieldExtractors = null)
    {
        $fieldExtractors = $fieldExtractors ?? [
            new FilterFieldExtractor(),
            new SortOrderFieldExtractor(),
        ];

        parent::__construct($fieldExtractors);
    }
}
