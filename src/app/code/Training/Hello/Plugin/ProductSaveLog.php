<?php
namespace Training\Hello\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Psr\Log\LoggerInterface;

class ProductSaveLog
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeSave(
        ProductRepositoryInterface $subject,
        ProductInterface $product
    ): array
    {
        $this->logger->info('Saving product: ' . $product->getSku());
        return [$product];
    }
}