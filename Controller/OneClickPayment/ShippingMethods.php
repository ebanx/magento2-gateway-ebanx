<?php
namespace DigitalHub\Ebanx\Controller\OneClickPayment;

class ShippingMethods extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
		\Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
		\Magento\Framework\Pricing\Helper\Data $priceHelper,
		\Magento\Customer\Model\Address $addressModel
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
		$this->cartRepositoryInterface = $cartRepositoryInterface;
		$this->priceCurrency = $priceCurrency;

		$this->paymentMethodManagement = $paymentMethodManagement;
		$this->priceHelper = $priceHelper;
		$this->addressModel = $addressModel;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();
		$cart_id = $this->getRequest()->getParam('cart_id');
		$address_id = $this->getRequest()->getParam('address_id');

		$quote = $this->cartRepositoryInterface->get($cart_id); // load empty cart quote

		$address = $this->addressModel->load($address_id);
		$addressData = $address->getData();

		//Set Address to quote
        $quote->getBillingAddress()->addData($addressData);
        $quote->getShippingAddress()->addData($addressData);

		$shippingAddress = $quote->getShippingAddress();
		$shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates();

		$ratesCollection = $quote->getShippingAddress()->getShippingRatesCollection();
		$rates = [];
		foreach($ratesCollection as $_rate){
            $rates[] = [
				'label' => $_rate->getMethodTitle() . ' - ' . $this->priceHelper->currency(number_format($_rate->getPrice(),2),true,false),
				'value' => $_rate->getCode()
			];
		}
		$quote->save();

        return $result->setData([
            'success' => true,
			'items' => $rates,
			'cart_id' => $cart_id,
			'address_id' => $address_id,
			'subtotal' => $quote->getBaseSubtotal(),
			'total' => $quote->getBaseGrandTotal()
        ]);
	}
}
