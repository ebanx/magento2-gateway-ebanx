<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxBoletoConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_boleto';

    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;

    /**
     * @var \Ebanx\Payments\Helper\Data
     */
    protected $_ebanxHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * EbanxBoletoConfigProvider constructor.
     *
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Ebanx\Payments\Helper\Data $ebanxHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request
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
                ],
                'ebanxBoleto' => [
                    'boletoTypes' => $this->_ebanxHelper->getBoletoTypes()
                ]
            ]
        ];
    }

    /**
     * Retrieve request object
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    protected function _getRequest()
    {
        return $this->_request;
    }
}
