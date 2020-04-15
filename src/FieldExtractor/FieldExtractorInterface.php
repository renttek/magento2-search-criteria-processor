<?php declare(strict_types=1);

namespace Renttek\SearchCriteriaProcessor\FieldExtractor;

use Magento\Framework\Api\SearchCriteriaInterface;

interface FieldExtractorInterface
{
    public function getFields(SearchCriteriaInterface $searchCriteria): array;
}
