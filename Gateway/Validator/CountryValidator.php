<?php
namespace Ebanx\Payments\Gateway\Validator;

use Ebanx\Payments\Gateway\Http\Client\Api;
use Magento\Payment\Gateway\Validator\CountryValidator as ParentValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\ConfigInterface;

class CountryValidator extends ParentValidator
{
    /**
     * @var \Magento\Payment\Gateway\ConfigInterface
     */
    private $config;

    /**
     * @var Api
     */
    private $api;

    /**
     * @param ResultInterfaceFactory $resultFactory
     * @param \Magento\Payment\Gateway\ConfigInterface $config
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        ConfigInterface $config,
        Api $api
    ) {
        $this->config = $config;
        $this->api = $api;
        parent::__construct($resultFactory, $config);
    }

    /**
     * @param array $validationSubject
     * @return bool
     * @throws NotFoundException
     * @throws \Exception
     */
    public function validate(array $validationSubject)
    {
        return $this->createResult(
            $this->api->isAvailableForCountry(
                $this->config->getValue('benjamin_gateway'),
                $validationSubject['country']
            )
        );
    }
}