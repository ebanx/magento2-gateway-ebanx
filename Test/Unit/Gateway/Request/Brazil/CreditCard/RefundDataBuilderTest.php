<?php
namespace DigitalHub\Ebanx\Test\Gateway\Request\Brazil\CreditCard;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Http;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;

use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Gateway\Request\Brazil\CreditCard\RefundDataBuilder;

class RefundDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(){
        Bootstrap::create(BP, $_SERVER)->createApplication(Http::class);
    }

    public function testBuild()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $hash = '4023947039740327402374032974';
        $amount = 1234.56;

        $expectation = [
            'amount' => $amount,
            'payment_hash' => $hash
        ];

        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelperMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $loggerMock = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $paymentModelMock->expects($this->any())
            ->method('getAdditionalInformation')
            ->with('transaction_data')
            ->willReturn([
                'payment' => [
                    'hash' => $hash
                ]
            ]);

        $paymentDOMock->expects($this->any())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentDOMock->expects($this->any())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $request = new RefundDataBuilder($ebanxHelperMock, $loggerMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock, 'amount' => $amount]) /* $buildSubject */
        );
    }
}
