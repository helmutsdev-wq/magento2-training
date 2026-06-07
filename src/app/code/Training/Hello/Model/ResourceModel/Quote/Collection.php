<?php
namespace Training\Hello\Model\ResourceModel\Quote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Training\Hello\Model\Quote as QuoteModel;
use Training\Hello\Model\ResourceModel\Quote as QuoteResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(QuoteModel::class, QuoteResource::class);
    }
}