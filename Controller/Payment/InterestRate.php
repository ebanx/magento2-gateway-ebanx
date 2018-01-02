<?php
namespace Ebanx\Payments\Controller\Payment;

use Ebanx\Payments\Gateway\Http\Client\Api;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;

class InterestRate extends Action
{
    /**
     * @var Json\Interceptor
     */
    private $jsonInterceptor;

    /**
     * @var \Ebanx\Benjamin\Services\Gateways\CreditCard
     */
    private $remoteGateway;

    public function __construct(
        Context $context,
        Api $api,
        Json\Interceptor $jsonInterceptor
    ) {
        parent::__construct($context);
        $this->jsonInterceptor = $jsonInterceptor;
        $this->remoteGateway = $api->benjamin()->creditCard();
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute() {
        $result = $this->jsonInterceptor;
        $request = $this->getRequest();
        $country = $request->getParam('country');
        $amount = $request->getParam('amount');

        return $result->setData(
            $this->remoteGateway->getPaymentTermsForCountryAndValue($country, $amount)
        );
    }
}
