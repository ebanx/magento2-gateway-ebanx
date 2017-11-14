<?php

namespace Ebanx\Payments\Model\Order;

use Magento\Framework\Model\AbstractModel;

class Payment extends AbstractModel
{
    protected function _construct() {
        $this->_init('Ebanx\Payments\Model\Resource\Order\Payment');
    }
}
