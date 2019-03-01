<?php

namespace DigitalHub\Ebanx\Gateway\Http\Util;

use Magento\Framework\App\ObjectManager;

class HttpUtil {
	public static function setupEbanxClient($config, $credit_card_config)
	{
		$ebanx_client = is_null($credit_card_config)
			? EBANX($config)
			: EBANX($config, $credit_card_config);
		$ebanx_client->setSource('Magento2', self::getEbanxVersion());

		return $ebanx_client;
	}

	private static function getEbanxVersion()
	{
		$object_manager = ObjectManager::getInstance();
		$module_list = $object_manager->get('Magento\Framework\Module\ModuleListInterface');
		$module_info = $module_list->getOne('DigitalHub_Ebanx');
		return $module_info['setup_version'];
	}
}
