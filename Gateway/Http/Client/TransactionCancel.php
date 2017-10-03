<?php
namespace Ebanx\Payments\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Ebanx\Payments\Helper\Data as EbanxData;
use Ebanx\Payments\Logger\EbanxLogger;

/**
 * Class TransactionSale
 */
class TransactionCancel implements ClientInterface
{
    /**
     * @var \Ebanx\Benjamin\Facade
     */
    protected $_benjamin;

    /**
     * PaymentRequest constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     * @param \Ebanx\Payments\Logger\EbanxLogger $ebanxLogger
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        EbanxData $ebanxHelper,
        EbanxLogger $ebanxLogger,
        array $data = []
    ) {
        $this->_encryptor = $encryptor;
        $this->_ebanxHelper = $ebanxHelper;
        $this->_ebanxLogger = $ebanxLogger;
        $this->_appState = $context->getAppState();

        $api = new Api($this->_ebanxHelper);
        $this->_benjamin = $api->benjamin();
    }

    /**
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     * @return null
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();

        // TODO: benjamin request capture
//        return $response;
    }
}