<?php
namespace DigitalHub\Ebanx\Controller\Checkout;

class Documentverification extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;
	protected $priceCurrency;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Checkout\Model\Session $session,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->_ebanxHelper = $ebanxHelper;
		$this->_session = $session;
		$this->_logger = $logger;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();

        $documentNumberField = $this->_ebanxHelper->getCustomerDocumentNumberField($this->_session->getQuote());
        $documentNumber = $this->_ebanxHelper->getCustomerDocumentNumber($this->_session->getQuote(), $documentNumberField);

        return $result->setData([
            'has_document_number' => $documentNumber ? true : false
        ]);
	}
}
