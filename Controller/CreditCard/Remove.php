<?php
namespace DigitalHub\Ebanx\Controller\CreditCard;

class Remove extends \Magento\Framework\App\Action\Action
{
    private $_messageManager;
    private $_customerSession;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\ResultFactory $result,
        \DigitalHub\Ebanx\Model\CreditCard\TokenFactory $tokenFactory
    )
	{
        $this->_result = $result;
        $this->_customerSession = $session;
        $this->_messageManager = $messageManager;
        $this->_tokenFactory = $tokenFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
        if($this->_customerSession->getCustomerId()){
            $tokenModel = $this->_tokenFactory->create();
			$item = $tokenModel->load((int)$this->getRequest()->getParam('id'));
            if($item->getCustomerId() == $this->_customerSession->getCustomerId()){
                try {
                    $item->delete();
                    $this->_messageManager->addSuccessMessage('The card has been removed');
                } catch (\Exception $e){
                    $this->_messageManager->addErrorMessage($e->getMessage());
                }
            }
		}

        $resultRedirect = $this->_result->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('digitalhub_ebanx/creditcard/saved');
        return $resultRedirect;
	}
}
