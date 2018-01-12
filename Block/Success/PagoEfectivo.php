<?php

namespace Ebanx\Payments\Block\Success;

class PagoEfectivo extends Base
{

    /**
     * @return string
     */
    public function getVoucherUrl()
    {
        $hash = $this->_ebanxPaymentCollection->getPaymentHashByOrderId($this->_orderId);
        $isSandbox = $this->_ebanxPaymentCollection->getEnvironmentByOrderId($this->_orderId) === 'sandbox';
        return $this->_urlBuilder->getUrl('ebanx/voucher/show', array(
            'hash' => $hash,
            'is_sandbox' => $isSandbox
        ));
    }


    /**
     * @return string
     */
    public function getDueDate() {
        return $this->_ebanxPaymentCollection->getDueDateByOrderId($this->_orderId, 'dd/MM');
    }
}
