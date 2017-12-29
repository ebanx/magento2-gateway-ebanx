<?php

namespace Ebanx\Payments\Model\Customer;

use Magento\Framework\Model\AbstractModel;

class Document extends AbstractModel
{
    protected function _construct() {
        $this->_init('Ebanx\Payments\Model\Resource\Customer\Document');
    }
}
