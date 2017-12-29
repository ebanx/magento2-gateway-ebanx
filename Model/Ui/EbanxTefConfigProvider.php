<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class EbanxTefConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_tef';

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
     * @param UrlInterface     $urlBuilder
     * @param RequestInterface $request
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequestInterface $request
    ) {
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
