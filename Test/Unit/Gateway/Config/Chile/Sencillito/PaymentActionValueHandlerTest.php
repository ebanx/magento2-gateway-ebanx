<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Config\Chile\Sencillito;

use DigitalHub\Ebanx\Gateway\Config\Chile\Sencillito\PaymentActionValueHandler;

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
