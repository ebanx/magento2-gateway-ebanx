<?php

namespace Ebanx\Payments\Block\Success;

class Redirect extends Base
{
    public function getRedirectUrl()
    {
        $hash = $this->_ebanxPaymentCollection->getPaymentHashByOrderId($this->_orderId);
        $paymentInfo = $this->ebanx->paymentInfo()->findByHash($hash);

        return $paymentInfo['payment']['redirect_url'];
    }
}
