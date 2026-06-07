<?php
namespace Training\Hello\Model;

use Training\Hello\Api\Data\QuoteInterface;
use Training\Hello\Api\QuoteRepositoryInterface;
use Training\Hello\Model\ResourceModel\Quote as QuoteResource;
use Training\Hello\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class QuoteRepository implements QuoteRepositoryInterface
{
    private QuoteFactory $quoteFactory;
    private QuoteResource $resource;
    private CollectionFactory $collectionFactory;
    private SearchResultsInterfaceFactory $searchResultsFactory;

    public function __construct(
        QuoteFactory $quoteFactory,
        QuoteResource $resource,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function save(QuoteInterface $quote): QuoteInterface
    {
        $this->resource->save($quote);
        return $quote;
    }

    public function getById(int $id): QuoteInterface
    {
        $quote = $this->quoteFactory->create();
        $this->resource->load($quote, $id);
        if (!$quote->getQuoteId()) {
            throw new NoSuchEntityException(__('Quote with id "%1" does not exist.', $id));
        }
        return $quote;
    }

    public function getList(SearchCriteriaInterface $criteria): \Magento\Framework\Api\SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $collection->addFieldToFilter($filter->getField(), [$filter->getConditionType() => $filter->getValue()]);
            }
        }
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    public function delete(QuoteInterface $quote): bool
    {
        $this->resource->delete($quote);
        return true;
    }

    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }
}
