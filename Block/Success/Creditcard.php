<?php

namespace Ebanx\Payments\Block\Success;

class Creditcard extends Base {

    public $currencyCode = 'BRL';

    public function getEbanxLocalAmount() {
        return $this->_ebanxPaymentCollection->getEbanxLocalAmountByOrderId($this->_orderId);
    }
}
