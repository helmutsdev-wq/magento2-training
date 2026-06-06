<?php

namespace Training\Hello\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Hello implements ArgumentInterface
{
    private ProductRepositoryInterface $productRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getGreeting()
    {
        return "Hello, Magento Ninja! The time is " . date('H:i:s');
    }
    

    public function getProductCount()
    {
        $products = $this->productRepository->getList(
            $this->searchCriteriaBuilder->create()
        );
        return $products->getTotalCount();
    }
}