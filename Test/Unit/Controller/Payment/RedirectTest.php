<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\Payment;

class RedirectTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkoutSession = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->setMethods(['getEbanxRedirectUrl', 'setEbanxRedirectUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $checkoutSession->expects($this->once())
            ->method('getEbanxRedirectUrl')
            ->willReturn('http://test.ebanx.com');

        $checkoutSession->expects($this->once())
            ->method('setEbanxRedirectUrl')
            ->with(null);

        $resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->setMethods(['setUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultMock->expects($this->once())
            ->method('setUrl')
            ->with('http://test.ebanx.com');

        $resultFactory->expects($this->once())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultMock);

        $controller = new \DigitalHub\Ebanx\Controller\Payment\Redirect($context, $checkoutSession, $resultFactory);

        $this->assertEquals(
            $resultMock,
            $controller->execute()
        );
    }
}
