<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Controller\Adminhtml\License;

use Magento\AdobeStockClientApi\Api\Data\LicenseConfirmationInterface;
use Magento\AdobeStockImageAdminUi\Controller\Adminhtml\License\Confirmation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Controller\Result\Json;
use Magento\Backend\App\Action\Context as ActionContext;

/**
 * License confirmation test.
 */
class ConfirmationTest extends TestCase
{
    /**
     * @var MockObject|ClientInterface $clientInterfaceMock
     */
    private $clientInterfaceMock;

    /**
     * @var MockObject|LoggerInterface $logger
     */
    private $logger;

    /**
     * @var MockObject|ActionContext $context
     */
    private $context;

    /**
     * @var Confirmation $confirmation
     */
    private $confirmation;

    /**
     * @var MockObject $request
     */
    private $request;

    /**
     * @var MockObject $resultFactory
     */
    private $resultFactory;

    /**
     * @var MockObject $jsonObject
     */
    private $jsonObject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->clientInterfaceMock = $this->createMock(ClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->context = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $this->request = $this->getMockForAbstractClass(
            \Magento\Framework\App\RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getParams']
        );
        $this->context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->context->expects($this->once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactory);

        $this->jsonObject = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactory->expects($this->once())->method('create')->with('json')->willReturn($this->jsonObject);
        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'isAjax' => "true",
                    'media_id' => 283415387,
                    'form_key' => "PyXOATf2fL9Y8iZf"
                ]
            );

        $this->confirmation = new Confirmation(
            $this->context,
            $this->clientInterfaceMock,
            $this->logger
        );
    }

    /**
     * Verify that Quota can be retrieved
     */
    public function testExecute()
    {
        /** @var LicenseConfirmationInterface|MockObject $confirmation */
        $confirmation = $this->createMock(LicenseConfirmationInterface::class);
        $confirmation->expects($this->once())->method('getMessage')->willReturn('message');
        $confirmation->expects($this->once())->method('getCanLicense')->willReturn(true);

        $data = [
            'success' => true,
            'result' => [
                'message' => 'message',
                'canLicense' => true
            ]
        ];
        $this->clientInterfaceMock->expects($this->once())
            ->method('getLicenseConfirmation')
            ->willReturn($confirmation);
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(200);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($data));
        $this->confirmation->execute();
    }

    /**
     * Verify that exception will throw if quota not available.
     */
    public function testExecuteWithException()
    {
        $result = [
            'success' => false,
            'message' => new Phrase('An error occurred during get quota operation. Contact support.')
        ];
        $this->clientInterfaceMock->expects($this->once())
            ->method('getLicenseConfirmation')
            ->willThrowException(new \Exception());
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(500);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($result));
        $this->confirmation->execute();
    }
}
