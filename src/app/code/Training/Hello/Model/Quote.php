<?php

namespace Training\Hello\Model;

use Training\Hello\Api\Data\QuoteInterface;
use Magento\Framework\Model\AbstractModel;
use Training\Hello\Model\ResourceModel\Quote as QuoteResource;

class Quote extends AbstractModel implements QuoteInterface
{
    protected function _construct()
    {
        $this->_init(QuoteResource::class);
    }
    
    public function getQuoteId(): ?int { return $this->getData('quote_id') ? (int) $this->getData('quote_id') : null; }
    
    public function setQuoteId(int $id): QuoteInterface { return $this->setData('quote_id', $id); }
    
    public function getQuoteText(): string { return (string) $this->getData('quote_text'); }
    
    public function setQuoteText(string $text): QuoteInterface { return $this->setData('quote_text', $text); }
    
    public function getCreatedAt(): string { return (string) $this->getData('created_at'); }
    
    public function setCreatedAt(string $createdAt): QuoteInterface { return $this->setData('created_at', $createdAt); }
}