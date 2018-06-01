<?php
namespace DigitalHub\Ebanx\Test\Unit\Logger;

class LoggerTest extends \PHPUnit\Framework\TestCase
{
    public function testAddRecord()
    {
        $loggerHandlers = [];
        $logger = new \DigitalHub\Ebanx\Logger\Logger('main', $loggerHandlers);

        $this->assertEquals(
            true,
            $logger->addRecord(100, 'Message', ['context'])
        );
    }
}
