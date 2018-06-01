<?php
namespace DigitalHub\Ebanx\Controller\OneClickPayment;

class PaymentMethods extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
		\Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
		\Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
		$this->cartRepositoryInterface = $cartRepositoryInterface;
		$this->paymentMethodManagement = $paymentMethodManagement;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();
		$postData = json_decode($this->getRequest()->getContent());
		$cart_id = $postData->cart_id;

		$quote = $this->cartRepositoryInterface->get($cart_id); // load empty cart quote

		$quote->getShippingAddress()->setShippingMethod($postData->shipping_method);
		$quote->collectTotals();
		$quote->save();

		$allowed_methods = [
			\DigitalHub\Ebanx\Model\Ui\Brazil\CreditCard\ConfigProvider::CODE,
			\DigitalHub\Ebanx\Model\Ui\Argentina\CreditCard\ConfigProvider::CODE,
			\DigitalHub\Ebanx\Model\Ui\Mexico\CreditCard\ConfigProvider::CODE,
			\DigitalHub\Ebanx\Model\Ui\Colombia\CreditCard\ConfigProvider::CODE
		];

		$paymentMethods = [];
        foreach ($this->paymentMethodManagement->getList($quote->getId()) as $paymentMethod) {
			if(in_array($paymentMethod->getCode(), $allowed_methods)){
				$paymentMethods[] = [
					'code' => $paymentMethod->getCode(),
					'title' => $paymentMethod->getTitle()
				];
			}
        }

        return $result->setData([
            'success' => true,
			'items' => $paymentMethods,
			'cart_id' => $cart_id,
			'subtotal' => $quote->getBaseSubtotal(),
			'total' => $quote->getBaseGrandTotal()
        ]);
	}
}
