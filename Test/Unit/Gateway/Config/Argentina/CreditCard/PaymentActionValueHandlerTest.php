<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Config\Argentina\CreditCard;

use DigitalHub\Ebanx\Gateway\Config\Argentina\CreditCard\PaymentActionValueHandler;

use DigitalHub\Ebanx\Helper\Data;

class PaymentActionValueHandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testHandleCapture()
    {
        $subject = [];
        $storeId = 1;

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->setMethods(['getConfigData'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->with('digitalhub_ebanx_global/cc', 'capture')
            ->willReturn(0);

        $valueHandler = new PaymentActionValueHandler($ebanxHelper);
        $this->assertEquals(
            'authorize_capture',
            $valueHandler->handle($subject, $storeId)
        );
    }
}
