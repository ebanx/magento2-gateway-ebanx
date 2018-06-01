<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\OneClickPayment;

class SessionCheckTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $customerId = 1;
        $isLoggedIn = true;

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $session = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->setMethods(['getCustomerId','isLoggedIn'])
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tokenFactory = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\TokenFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenModel = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\Token::class)
            ->setMethods(['customerHasToken'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenModel->expects($this->once())
            ->method('customerHasToken')
            ->with($customerId)
            ->willReturn(true);

        $tokenFactory->expects($this->once())
            ->method('create')
            ->willReturn($tokenModel);

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'loggedin' => true,
                'has_saved_cards' => true
            ]);

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $session->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $session->expects($this->any())
            ->method('isLoggedIn')
            ->willReturn($isLoggedIn);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\SessionCheck($context, $resultJsonFactory, $ebanxHelper, $session, $logger, $tokenFactory);

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }

    public function testExecuteNoToken()
    {
        $customerId = 1;
        $isLoggedIn = true;

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $session = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->setMethods(['getCustomerId','isLoggedIn'])
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tokenFactory = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\TokenFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenModel = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\Token::class)
            ->setMethods(['customerHasToken'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenModel->expects($this->once())
            ->method('customerHasToken')
            ->with($customerId)
            ->willReturn(false);

        $tokenFactory->expects($this->once())
            ->method('create')
            ->willReturn($tokenModel);

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'loggedin' => true,
                'has_saved_cards' => false
            ]);

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $session->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $session->expects($this->any())
            ->method('isLoggedIn')
            ->willReturn($isLoggedIn);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\SessionCheck($context, $resultJsonFactory, $ebanxHelper, $session, $logger, $tokenFactory);

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }

    public function testExecuteNotLogged()
    {
        $customerId = 0;
        $isLoggedIn = true;

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $session = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->setMethods(['getCustomerId','isLoggedIn'])
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tokenFactory = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\TokenFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'loggedin' => false
            ]);

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $session->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $session->expects($this->any())
            ->method('isLoggedIn')
            ->willReturn($isLoggedIn);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\SessionCheck($context, $resultJsonFactory, $ebanxHelper, $session, $logger, $tokenFactory);

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }
}
