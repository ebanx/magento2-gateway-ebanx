<?php
namespace Ebanx\Payments\Gateway\Http\Client;

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
     * PaymentRequest constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     * @param \Ebanx\Payments\Logger\EbanxLogger $ebanxLogger
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
     * @return mixed
     * @throws \Magento\Payment\Gateway\Http\ClientException
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();
        var_dump($request);

        // TODO: benjamin request authorization
        return ['error' => 'test'];
    }
}
