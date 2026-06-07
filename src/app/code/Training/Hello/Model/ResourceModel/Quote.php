<?php

namespace Training\Hello\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Quote extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('training_hello_quote', 'quote_id');
    }
}