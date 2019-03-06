<?php

namespace DigitalHub\Ebanx\Controller\Plugincheck;

use DigitalHub\Ebanx\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\JsonFactory;

class View extends Action
{
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	) {
		parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
	}

	public function execute() {
		$object_manager = ObjectManager::getInstance();
		$jsonResult = $this->resultJsonFactory->create();
		$jsonResult->setData([
			'Magento2' => self::getMagentoVersion($object_manager),
			'ebanx-gateway' => self::getEbanxVersion($object_manager),
			'php'     => phpversion(),
			'mysql'   => self::getDBVersion($object_manager),
			'plugins' => self::getModulesList($object_manager),
			'configs' => self::getConfigs($object_manager),
		]);
		return $jsonResult;
	}

	private static function getMagentoVersion(ObjectManager $object_manager) {
		$product_metadata = $object_manager->get('Magento\Framework\App\ProductMetadataInterface');
		return $product_metadata->getVersion();

	}

	private static function getDBVersion(ObjectManager $object_manager) {
		$db_onnection = $object_manager->get('Magento\Framework\App\ResourceConnection')->getConnection();
		return $db_onnection->fetchAll('SELECT version() AS version')[0]['version'];
	}

	private static function getModulesList(ObjectManager $object_manager) {
		$module_list    = $object_manager->get('Magento\Framework\Module\FullModuleList')->getAll();
		$modules        = [];
		foreach ($module_list as $module) {
			$module_name = $module['name'];
			array_push($modules, [$module_name =>$module['setup_version']]);
		}
		return $modules;
	}

	private static function getPaymentMethods($ebanx_helper) {
		$all_payment_methods = [];
		$countries = [
			['brazil', 'br-'],
			['argentina', 'ar-'],
			['chile',  'ch-'],
			['colombia', 'co-'],
			['ecuador', 'ec-'],
			['peru', 'pe-'],
			['mexico', 'mx-'],
		];
		foreach($countries as $country)	{
			$payment_methods = explode(",", $ebanx_helper->getConfigData('digitalhub_ebanx_global', 'payments_' . $country[0]));
			foreach ($payment_methods as $method) {
				if($method){
					array_push($all_payment_methods, $country[1] . $method);
				}
			}
		}
		return $all_payment_methods;
	}

	private static function getConfigs(ObjectManager $object_manager) {
		$ebanx_helper = self::getEbanxHelper($object_manager);
		return [
			'max_installment'       => $ebanx_helper->getConfigData('digitalhub_ebanx_global/cc', 'max_installments'),
			'sandbox_mode'          => $ebanx_helper->getConfigData('digitalhub_ebanx_global', 'sandbox') === "1",
			'save_card_data'        => $ebanx_helper->getConfigData('digitalhub_ebanx_global/cc', 'save') === "1",
			'one_click'             => $ebanx_helper->getConfigData('digitalhub_ebanx_global/cc', 'one_click_payment') === "1",
			'capture_enabled'       => $ebanx_helper->getConfigData('digitalhub_ebanx_global/cc', 'capture') === "1",
			'show_local_amount'     => $ebanx_helper->getConfigData('digitalhub_ebanx_global', 'show_local_total') === "1",
			'show_iof'              => $ebanx_helper->getConfigData('digitalhub_ebanx_global', 'show_iof') === "1",
			'enabled_payment_types' => self::getPaymentMethods($ebanx_helper),
		];
	}

	private static function getEbanxHelper(ObjectManager $object_manager) {
		$module_list = $object_manager->get('Magento\Framework\Module\ModuleListInterface');
		$context = $object_manager->get('Magento\Framework\App\Helper\Context');
		return new Data($context, $module_list);
	}

	private static function getEbanxVersion(ObjectManager $object_manager)
	{
		$module_list = $object_manager->get('Magento\Framework\Module\ModuleListInterface');
		$module_info = $module_list->getOne('DigitalHub_Ebanx');
		return $module_info['setup_version'];
	}
}
