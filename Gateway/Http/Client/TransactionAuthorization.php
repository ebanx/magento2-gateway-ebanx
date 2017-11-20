<?php
namespace Ebanx\Payments\Gateway\Http\Client;

use Ebanx\Benjamin\Models\Payment;
use Ebanx\Payments\Model\Order\Payment as EbanxPaymentModel;
use Ebanx\Payments\Model\Resource\Order\Payment as EbanxResourceModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Ebanx\Payments\Helper\Data as EbanxHelper;
use Ebanx\Payments\Logger\EbanxLogger;
use Magento\Payment\Gateway\Http\TransferInterface;

/**
 * Class TransactionSale
 */
class TransactionAuthorization implements ClientInterface
{
    /**
     * @var \Ebanx\Benjamin\Facade
     */
    protected $_benjamin;
    /**
     * @var EncryptorInterface $_encryptor
     */
    protected $_encryptor;
    /**
     * @var EbanxHelper $_ebanxHelper
     */
    protected $_ebanxHelper;
    /**
     * @var EbanxLogger $_ebanxLogger
     */
    protected $_ebanxLogger;
    /**
     * @var \Magento\Framework\App\State $_appState
     */
    protected $_appState;
    /**
     * @var EbanxPaymentModel
     */
    protected $_ebanxPaymentModel;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var EbanxResourceModel
     */
    protected $_ebanxResourceModel;

    /**
     * PaymentRequest constructor.
     *
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param EbanxHelper $ebanxHelper
     * @param EbanxLogger $ebanxLogger
     * @param EbanxPaymentModel $ebanxPaymentModel
     * @param ScopeConfigInterface $scopeConfig
     * @param EbanxResourceModel $ebanxResourceModel
     * @param Api $api
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        EbanxHelper $ebanxHelper,
        EbanxLogger $ebanxLogger,
        EbanxPaymentModel $ebanxPaymentModel,
        ScopeConfigInterface $scopeConfig,
        EbanxResourceModel $ebanxResourceModel,
        Api $api
    ) {
        $this->_encryptor = $encryptor;
        $this->_ebanxHelper = $ebanxHelper;
        $this->_ebanxLogger = $ebanxLogger;
        $this->_appState = $context->getAppState();
        $this->_ebanxPaymentModel = $ebanxPaymentModel;
        $this->_scopeConfig = $scopeConfig;
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

        $response = $this->_benjamin->boleto()->create($payment);

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
                          ->setBarCode($paymentResponse['boleto_barcode'])
                          ->setInstalments($paymentResponse['instalments'])
                          ->setEnvironment($mode)
                          ->setCustomerDocument($paymentResponse['customer']['document'])
                          ->setLocalAmount($paymentResponse['amount_br']);
        $this->_ebanxResourceModel->save($this->_ebanxPaymentModel);
    }
}
