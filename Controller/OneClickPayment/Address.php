<?php
namespace DigitalHub\Ebanx\Controller\OneClickPayment;

class Address extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;
	protected $session;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
		\Magento\Customer\Model\Session $session
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
		$this->session = $session;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();

		$items = [];

		if($this->session->getCustomerId()){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($this->session->getCustomerId());

			foreach ($customerObj->getAddresses() as $address)
			{
				$street_line = implode(', ', $address->getStreet());
				$address_line = implode(', ', [
					$street_line,
					$address->getCity(),
					$address->getRegionCode(),
					$address->getCountry(),
					$address->getPostcode()
				]);
				$items[] = [
					'label' => $address_line,
					'value' => $address->getId()
				];
			}
		}


        return $result->setData([
            'success' => true,
			'items' => $items
        ]);
	}
}
