<?php
namespace DigitalHub\Ebanx\Controller\Notification;

class Status extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;
	protected $priceCurrency;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Framework\Event\Manager $eventManager
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
		$this->_eventManager = $eventManager;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();

        $postData = $this->getRequest()->getPost();

        try {
            $operation = $postData['operation'];
            $notification_type = $postData['notification_type'];
            $hash_codes = explode(',',$postData['hash_codes']);

            if($operation == 'payment_status_change' && $notification_type == 'update' && count($hash_codes)){
                foreach($hash_codes as $hash){
                    $transaction_data = ['hash' => $hash];
                    $data = new \Magento\Framework\DataObject($transaction_data);
                    $this->_eventManager->dispatch('digitalhub_ebanx_notification_status_change', ['transaction_data' => $data]);
                }
            }

            $result->setData([
                'hash_codes' => $hash_codes,
                'success' => true
            ]);
        } catch (\Exception $e){
            $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST);
            $result->setData([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }

        return $result;
	}
}
