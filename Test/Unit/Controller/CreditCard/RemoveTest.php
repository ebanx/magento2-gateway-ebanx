<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\CreditCard;

class RemoveTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $requested = [
            'customer_id' => 1,
            'token' => [
                'id' => 1,
                'customer_id' => 1
            ]
        ];

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->setMethods(['getParam'])
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $messageManager = $this->getMockForAbstractClass(\Magento\Framework\Message\ManagerInterface::class);

        $resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenFactory = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\TokenFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenModelMock = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\Token::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tokenFactory->expects($this->once())
            ->method('create')
            ->willReturn($tokenModelMock);

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($requested['token']['id']);

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        $session->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($requested['customer_id']);

        $tokenMock = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\Token::class)
            ->setMethods(['delete','getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($requested['token']['customer_id']);

        $tokenMock->expects($this->once())
            ->method('delete');

        $tokenModelMock->expects($this->once())
            ->method('load')
            ->with($requested['token']['id'])
            ->willReturn($tokenMock);

        $messageManager->expects($this->once())
            ->method('addSuccessMessage')
            ->with('The card has been removed');

        $resultRedirect = $this->getMockBuilder(\Magento\Framework\Controller\Result::class)
            ->setMethods(['setPath'])
            ->getMock();

        $resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('digitalhub_ebanx/creditcard/saved');

        $resultFactory->expects($this->once())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirect);

        $controller = new \DigitalHub\Ebanx\Controller\CreditCard\Remove($context, $session, $messageManager, $resultFactory, $tokenFactory);

        $this->assertEquals(
            $resultRedirect,
            $controller->execute()
        );
    }

    public function testExecuteWithException()
    {
        $requested = [
            'customer_id' => 1,
            'token' => [
                'id' => 1,
                'customer_id' => 1
            ]
        ];

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->setMethods(['getParam'])
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $messageManager = $this->getMockForAbstractClass(\Magento\Framework\Message\ManagerInterface::class);

        $resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenFactory = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\TokenFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenModelMock = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\Token::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tokenFactory->expects($this->once())
            ->method('create')
            ->willReturn($tokenModelMock);

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($requested['token']['id']);

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        $session->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($requested['customer_id']);

        $tokenMock = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\Token::class)
            ->setMethods(['delete','getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($requested['token']['customer_id']);

        $tokenMock->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new \Exception('Error Message')));

        $tokenModelMock->expects($this->once())
            ->method('load')
            ->with($requested['token']['id'])
            ->willReturn($tokenMock);

        $messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with('Error Message');

        $resultRedirect = $this->getMockBuilder(\Magento\Framework\Controller\Result::class)
            ->setMethods(['setPath'])
            ->getMock();

        $resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('digitalhub_ebanx/creditcard/saved');

        $resultFactory->expects($this->once())
            ->method('create')
            ->with(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirect);

        $controller = new \DigitalHub\Ebanx\Controller\CreditCard\Remove($context, $session, $messageManager, $resultFactory, $tokenFactory);

        $this->assertEquals(
            $resultRedirect,
            $controller->execute()
        );
    }
}
