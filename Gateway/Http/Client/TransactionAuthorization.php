<?php
namespace Ebanx\Payments\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Ebanx\Payments\Helper\Data as EbanxData;
use Monolog\Logger;

/**
 * Class TransactionSale
 */
class TransactionAuthorization implements ClientInterface
{

    /**
     * @var \Ebanx\Benajmin
     */
    protected $_benjamin;

    /**
     * PaymentRequest constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     * @param \Monolog\Logger $ebanxLogger
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        EbanxData $ebanxHelper,
        Logger $ebanxLogger,
        array $data = []
    ) {
        $this->_encryptor = $encryptor;
        $this->_ebanxHelper = $ebanxHelper;
        $this->_ebanxLogger = $ebanxLogger;
        $this->_appState = $context->getAppState();

        // TODO: Connect Benjamin
//        $benjamin = new \Ebanx\Benjamin();

//        $this->_client = $benjamin;
    }

    /**
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     * @return mixed
     * @throws ClientException
     */
    public function placeRequest(\Magento\Payment\Gateway\Http\TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();

        // TODO: benjamin request authorization
//        return $response;
    }
}
