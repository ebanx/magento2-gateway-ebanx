<?php
namespace Ebanx\Payments\Gateway\Http\Client;

use Ebanx\Benjamin\Models\Payment;
use Ebanx\Payments\Model\Order\Payment as EbanxPaymentModel;
use Ebanx\Payments\Model\Resource\Order\Payment as EbanxResourceModel;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Ebanx\Payments\Helper\Data as EbanxHelper;
use Magento\Payment\Gateway\Http\TransferInterface;

/**
 * Class TransactionSale
 */
class SafetyPayTransactionAuthorization implements ClientInterface
{
    /**
     * @var \Ebanx\Benjamin\Facade
     */
    protected $_benjamin;
    /**
     * @var EbanxHelper $_ebanxHelper
     */
    protected $_ebanxHelper;
    /**
     * @var EbanxPaymentModel
     */
    protected $_ebanxPaymentModel;
    /**
     * @var EbanxResourceModel
     */
    protected $_ebanxResourceModel;

    /**
     * PaymentRequest constructor.
     *
     * @param EbanxHelper $ebanxHelper
     * @param EbanxPaymentModel $ebanxPaymentModel=
     * @param EbanxResourceModel $ebanxResourceModel
     * @param Api $api
     */
    public function __construct(
        EbanxHelper $ebanxHelper,
        EbanxPaymentModel $ebanxPaymentModel,
        EbanxResourceModel $ebanxResourceModel,
        Api $api
    ) {
        $this->_ebanxHelper = $ebanxHelper;
        $this->_ebanxPaymentModel = $ebanxPaymentModel;
        $this->_ebanxResourceModel = $ebanxResourceModel;

        $this->_benjamin = $api->benjamin();
    }

    /**
     * @param TransferInterface $transferObject
     *
     * @return mixed
     * @throws CouldNotSaveException
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $payment = new Payment($transferObject->getBody());

        $paymentMethodName = 'safetyPay' . ucfirst($transferObject->getBody()['safetyPayType']);
        $response          = $this->_benjamin->{$paymentMethodName}()->create($payment);

        if ($response['status'] !== 'SUCCESS') {
            throw new CouldNotSaveException(__($response['status_code'] . ': ' . $response['status_message']));
        }

        $this->persistPayment($response['payment']);

        return $response['payment'];
    }



    private function persistPayment($paymentResponse) {
        $mode = $this->_ebanxHelper->getEbanxAbstractConfigData('mode') ? 'sandbox' : 'live';
        $this->_ebanxPaymentModel->setPaymentHash($paymentResponse['hash'])
                                 ->setOrderId($paymentResponse['order_number'])
                                 ->setDueDate($paymentResponse['due_date'])
                                 ->setInstalments($paymentResponse['instalments'])
                                 ->setEnvironment($mode)
                                 ->setCustomerDocument($paymentResponse['customer']['document'])
                                 ->setLocalAmount($paymentResponse['amount_br']);
        $this->_ebanxResourceModel->save($this->_ebanxPaymentModel);
    }
}
