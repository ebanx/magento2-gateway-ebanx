<?php

namespace DigitalHub\Ebanx\Gateway\Http\Util;

class HttpUtil {
	public static function setupEbanxClient($config, $credit_card_config)
	{
		$ebanx_client = is_null($credit_card_config)
			? EBANX($config)
			: EBANX($config, $credit_card_config);
		$ebanx_client->setSource('Magento2', self::getMagentoVersion());

		return $ebanx_client;
	}

	private static function getMagentoVersion()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');

		return $productMetadata->getVersion();
	}
}
