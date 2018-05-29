<?php
namespace DigitalHub\Ebanx\Controller\OneClickPayment;

class SessionCheck extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;
	protected $session;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
		\Magento\Customer\Model\Session $session,
		\DigitalHub\Ebanx\Logger\Logger $logger
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
		$this->session = $session;
		$this->logger = $logger;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();

		if($this->session->getCustomerId() && $this->session->isLoggedIn()){
            $result->setData([
                'loggedin' => true
            ]);
		} else {
            $result->setData([
                'loggedin' => false
            ]);
        }
        return $result;
	}
}
