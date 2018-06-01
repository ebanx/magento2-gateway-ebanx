<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Config\Colombia\Eft;

use DigitalHub\Ebanx\Gateway\Config\Colombia\Eft\PaymentActionValueHandler;

use DigitalHub\Ebanx\Helper\Data;

class PaymentActionValueHandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testHandle()
    {
        $subject = [];
        $storeId = 1;

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $valueHandler = new PaymentActionValueHandler($ebanxHelper);
        $this->assertEquals(
            'authorize',
            $valueHandler->handle($subject, $storeId)
        );
    }
}
