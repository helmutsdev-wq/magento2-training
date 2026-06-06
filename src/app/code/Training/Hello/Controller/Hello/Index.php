<?php

namespace Training\Hello\Controller\Hello;

use Magento\Framework\App\Action\Action;

class Index extends Action
{
    public function execute()
    {
        return $this->resultFactory->create(
            \Magento\Framework\Controller\ResultFactory::TYPE_PAGE
        );
    }
}
