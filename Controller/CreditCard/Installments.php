<?php
namespace DigitalHub\Ebanx\Controller\CreditCard;

class Installments extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;
	protected $priceCurrency;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
		$this->priceCurrency = $priceCurrency;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();

		$maxInstallments = $this->ebanxHelper->getConfigData('digitalhub_ebanx_global/cc', 'max_installments');
		$minInstallmentValue = $this->ebanxHelper->getConfigData('digitalhub_ebanx_global/cc', 'min_installment_value');
		$total = (float)$this->getRequest()->getParam('total', 0);

		if(!(int)$maxInstallments){
			$maxInstallments = 1;
		}

		$installments = [];

		foreach(range(1,$maxInstallments) as $number){
			$total_with_interest = $this->ebanxHelper->calculateTotalWithInterest($total, $number);
			$installment_total = $total_with_interest / $number;
			if($installment_total < $minInstallmentValue) continue;

			$installments[] = [
				'number' => $number,
				'installment_value' => $installment_total,
				'total_with_interest' => $total_with_interest,
				'interest' => (float)$this->ebanxHelper->getInterestRateFor($number)
			];
		}

        return $result->setData([
            'success' => $total && count($installments) ? true : false,
			'installments' => $installments,
        ]);
	}
}
