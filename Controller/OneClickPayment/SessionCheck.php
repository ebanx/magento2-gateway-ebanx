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
		\DigitalHub\Ebanx\Logger\Logger $logger,
		\DigitalHub\Ebanx\Model\CreditCard\TokenFactory $tokenFactory
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
		$this->session = $session;
		$this->logger = $logger;
		$this->tokenFactory = $tokenFactory;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();

		if($this->session->getCustomerId() && $this->session->isLoggedIn()){

			$token = $this->tokenFactory->create();
	        $hasValidToken = $token->customerHasToken($this->session->getCustomerId());

            $result->setData([
                'loggedin' => true,
				'has_saved_cards' => $hasValidToken
            ]);
		} else {
            $result->setData([
                'loggedin' => false
            ]);
        }
        return $result;
	}
}
