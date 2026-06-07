<?php

namespace Training\Hello\Api;

use Training\Hello\Api\Data\QuoteInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Api\SearchResultsInterface;

interface QuoteRepositoryInterface
{
    public function save(QuoteInterface $quote): QuoteInterface;
    public function getById(int $id): QuoteInterface;
    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface;
    public function delete(QuoteInterface $quote): bool;
    public function deleteById(int $id): bool;
}