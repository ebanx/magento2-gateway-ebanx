<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\Notification;

class StatusTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $postData = [
            'operation' => 'payment_status_change',
            'notification_type' => 'update',
            'hash_codes' => '32984653489569,50394750347598346,05983470589346589'
        ];

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
		$ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager = $this->getMockBuilder(\Magento\Framework\Event\Manager::class)
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $fakeRequest = new \Magento\Framework\DataObject();
        $fakeRequest->setData([
            'post' => $postData
        ]);

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn($fakeRequest);

        $fakeTransactionData = new \Magento\Framework\DataObject();

        $eventManager->expects($this->any())
            ->method('dispatch')
            ->withConsecutive(
                ['digitalhub_ebanx_notification_status_change', [
                    'transaction_data' => new \Magento\Framework\DataObject([
                        'hash' => '32984653489569'
                    ])
                ]],
                ['digitalhub_ebanx_notification_status_change', [
                    'transaction_data' => new \Magento\Framework\DataObject([
                        'hash' => '50394750347598346'
                    ])
                ]],
                ['digitalhub_ebanx_notification_status_change', [
                    'transaction_data' => new \Magento\Framework\DataObject([
                        'hash' => '05983470589346589'
                    ])
                ]]
            );

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'success' => true
            ])
            ->willReturn($resultJson);

        $controller = new \DigitalHub\Ebanx\Controller\Notification\Status($context, $resultJsonFactory, $ebanxHelper, $eventManager);

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }

    public function testExecuteWithError()
    {
        $postData = [
            'operation' => 'payment_status_change',
            'notification_type' => 'update',
            'hash_codes' => '32984653489569,50394750347598346,05983470589346589'
        ];

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
		$ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager = $this->getMockBuilder(\Magento\Framework\Event\Manager::class)
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $fakeRequest = new \Magento\Framework\DataObject();
        $fakeRequest->setData([
            'post' => $postData
        ]);

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn($fakeRequest);

        $fakeTransactionData = new \Magento\Framework\DataObject();

        $eventManager->expects($this->any())
            ->method('dispatch')
            ->will($this->throwException(new \Exception('Error Message')));

        $resultJson->expects($this->once())
            ->method('setHttpResponseCode')
            ->with(\Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST);

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'error' => true,
                'message' => 'Error Message'
            ])
            ->willReturn($resultJson);

        $controller = new \DigitalHub\Ebanx\Controller\Notification\Status($context, $resultJsonFactory, $ebanxHelper, $eventManager);

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }
}
