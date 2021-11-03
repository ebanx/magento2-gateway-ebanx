<?php

namespace DigitalHub\Ebanx\Controller\Notification;
use Magento\Framework\App\RequestInterface;

class Status extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $ebanxHelper;
    protected $priceCurrency;
    protected $_logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->ebanxHelper = $ebanxHelper;
        $this->_eventManager = $eventManager;
        $this->_logger = $logger;

        if (interface_exists('\Magento\Framework\App\CsrfAwareActionInterface')) {
            $request = $this->getRequest();
            if ($request instanceof RequestInterface && $request->isPost() && empty($request->getParam('form_key'))) {
                $formKey = $this->_objectManager->get(\Magento\Framework\Data\Form\FormKey::class);
                $request->setParam('form_key', $formKey->getFormKey());
            }
        }
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $requestData = $this->getRequest()->getParams();

        try {
            if (empty($requestData)) {
                throw new \Exception('No params found.');
            }

            $operation = $requestData['operation'];
            $notification_type = $requestData['notification_type'];
            $hash_codes = explode(',', $requestData['hash_codes']);
            $notification = new \Ebanx\Benjamin\Models\Notification($operation, $notification_type, $hash_codes);

            if (!\Ebanx\Benjamin\Util\Http::isValidNotification($notification)) {
                throw new \Exception('Invalid params.');
            }

            foreach ($hash_codes as $hash) {
                $transaction_data = ['hash' => $hash];
                $data = new \Magento\Framework\DataObject($transaction_data);
                $this->_eventManager->dispatch('digitalhub_ebanx_notification_status_change', [
                    'transaction_data' => $data,
                    'notification_data' => $notification_type,
                ]);
            }

            $result->setData([
                'success' => true
            ]);
        } catch (\Magento\Sales\Exception\DocumentValidationException $e) {
            $result->setData([
                'success' => true
            ]);

            $this->_logger->info(sprintf('EBANX Exception `%s` :: `%s`', get_class($e), __METHOD__), [$e->getMessage()]);
        } catch (\Exception $e){
            $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST);
            $result->setData([
                'error' => true,
                'message' => $e->getMessage()
            ]);

            $this->_logger->info(sprintf('EBANX Exception `%s` :: `%s`', get_class($e), __METHOD__), [$e->getMessage()]);
        }

        return $result;
    }
}
