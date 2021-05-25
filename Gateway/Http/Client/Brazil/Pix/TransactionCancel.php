<?php
namespace DigitalHub\Ebanx\Gateway\Http\Client\Brazil\Pix;

use Magento\Payment\Gateway\Http\ClientInterface;

/**
 * Class TransactionCancel
 */
class TransactionCancel implements ClientInterface
{

    protected $_logger;

    /**
     * PaymentRequest constructor.
     *
     * @param \DigitalHub\Ebanx\Logger\Logger $logger
     */
    public function __construct(
        \DigitalHub\Ebanx\Logger\Logger $logger
    ) {
        $this->_logger = $logger;
        $this->_logger->info('Client Cancel :: __construct');
    }

    /**
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     * @return mixed
     * @throws ClientException
     */
    public function placeRequest(\Magento\Payment\Gateway\Http\TransferInterface $transferObject)
    {
        $this->_logger->info('Client Cancel :: placeRequest');

        return [];
    }
}
