<?php
namespace DigitalHub\Ebanx\Logger;
class Logger extends \Monolog\Logger
{
    public function addRecord($level, $message, array $context = array())
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$ebanxHelper = $objectManager->create('DigitalHub\Ebanx\Helper\Data');

        if((int)$ebanxHelper->getConfigData('digitalhub_ebanx_global', 'debug')){
            return parent::addRecord($level, $message, $context);
        }
        return true;
    }
}
