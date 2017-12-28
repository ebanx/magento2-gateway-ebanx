<?php
namespace Ebanx\Payments\Model\Ui;

use Ebanx\Payments\Helper\Data;
use Ebanx\Payments\Model\Resource\Customer\Document\Collection as DocumentCollection;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;

class DocumentConfigProvider implements ConfigProviderInterface
{
    const CODE = 'ebanx_document';

    private $customerSession;

    private $customer;
    private $collection;

    public function __construct(
        Customer $customer,
        DocumentCollection $collection,
        Session $session
    ) {
        $this->customer = $customer;
        $this->collection = $collection;
        $this->customerSession = $session;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'ebanx' => [
                    'customerDocument' => $this->fetchDocumentFromSession()
                ]
            ]
        ];
    }

    private function fetchDocumentFromSession()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return '';
        }

        return $this->collection->getDocumentForCustomerId(
            $this->customerSession->getCustomerId()
        );
    }
}
