<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use DigitalHub\Ebanx\Gateway\Http\TransferFactory;

class TransferFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $request = [
            'parameter1' => 'value1',
            'parameter2' => 'value2'
        ];

        $transferBuilder = $this->getMockBuilder(TransferBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $transferObject = $this->getMockBuilder(TransferInterface::class)->getMock();

        $transferBuilder->expects(static::once())
            ->method('setBody')
            ->with($request)
            ->willReturnSelf();

        $transferBuilder->expects(static::once())
            ->method('build')
            ->willReturn($transferObject);

        $transferFactory = new TransferFactory($transferBuilder);

        static::assertSame(
            $transferObject,
            $transferFactory->create($request)
        );
    }
}
