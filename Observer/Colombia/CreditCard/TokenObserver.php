<?php
namespace DigitalHub\Ebanx\Observer\Colombia\CreditCard;

class TokenObserver implements \Magento\Framework\Event\ObserverInterface
{
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$tokenData = $observer->getData('token_data');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $token = $objectManager->create('DigitalHub\Ebanx\Model\CreditCard\Token');
        $token->setCustomerId($tokenData->getCustomerId());
        $token->setPaymentMethod(\DigitalHub\Ebanx\Model\Ui\Colombia\CreditCard\ConfigProvider::CODE);
        $token->setPaymentTypeCode($tokenData->getPaymentTypeCode());
        $token->setMaskedCardNumber($tokenData->getMaskedCardNumber());
        $token->setToken($tokenData->getToken());
        $token->save();

		return $this;
	}
}
