<?php
namespace Ebanx\Payments\Model\Ui;

use Ebanx\Payments\Helper\Data as EbanxData;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data;

class EbanxBoletoConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_boleto';

    /**
     * @var Data
     */
    protected $_paymentHelper;

    /**
     * @var EbanxData
     */
    protected $_ebanxHelper;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Request object
     *
     * @var RequestInterface
     */
    protected $_request;

    /**
     * EbanxBoletoConfigProvider constructor.
     *
     * @param Data             $paymentHelper
     * @param EbanxData        $ebanxHelper
     * @param UrlInterface     $urlBuilder
     * @param RequestInterface $request
     */
    public function __construct(
        Data $paymentHelper,
        EbanxData $ebanxHelper,
        UrlInterface $urlBuilder,
        RequestInterface $request
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_ebanxHelper = $ebanxHelper;
        $this->_urlBuilder = $urlBuilder;
        $this->_request = $request;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        // set to active
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => true,
                    'redirectUrl' => $this->_urlBuilder->getUrl(
                        'checkout/onepage/success/', ['_secure' => $this->_getRequest()->isSecure()])
                ]
            ]
        ];
    }

    /**
     * Retrieve request object
     *
     * @return RequestInterface
     */
    protected function _getRequest()
    {
        return $this->_request;
    }
}
