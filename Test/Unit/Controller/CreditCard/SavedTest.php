<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\CreditCard;

class SavedTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pageFactory = $this->getMockBuilder(\Magento\Framework\View\Result\PageFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultPage = $this->getMockBuilder(\Magento\Framework\View\Result\Page::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pageFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultPage);

        $controller = new \DigitalHub\Ebanx\Controller\CreditCard\Saved($context, $pageFactory);

        $this->assertEquals(
            $resultPage,
            $controller->execute()
        );
    }
}
