<?php
namespace Ebanx\Payments\Gateway\Http\Client;

use Ebanx\Benjamin\Models\Payment;
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
     * PaymentRequest constructor.
     *
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param EbanxHelper $ebanxHelper
     * @param EbanxLogger $ebanxLogger
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        EbanxHelper $ebanxHelper,
        EbanxLogger $ebanxLogger
    ) {
        $this->_encryptor = $encryptor;
        $this->_ebanxHelper = $ebanxHelper;
        $this->_ebanxLogger = $ebanxLogger;
        $this->_appState = $context->getAppState();

        $api = new Api($this->_ebanxHelper);
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

        return $response['payment'];
    }
}
