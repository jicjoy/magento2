<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Test\Unit\Model\Update;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Staging\Model\Update\Validator
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Staging\Model\Update
     */
    protected $entityMock;

    protected function setUp()
    {
        $this->entityMock = $this->createMock(\Magento\Staging\Model\Update::class);
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(\Magento\Staging\Model\Update\Validator::class, []);
    }

    /**
     * @expectedException \Magento\Framework\Exception\ValidatorException
     * @expectedExceptionMessage Please select Name for Future Update.
     */
    public function testValidateWithEmptyName()
    {
        $this->entityMock->expects($this->once())->method('getName')->willReturn('');
        $this->model->validateCreate($this->entityMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\ValidatorException
     * @expectedExceptionMessage Please select Start Time for Future Update.
     */
    public function testValidateWithEmptyStartTime()
    {
        $this->entityMock->expects($this->once())->method('getName')->willReturn('Test Update');
        $this->model->validateCreate($this->entityMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\ValidatorException
     * @expectedExceptionMessage Future Update Start Time cannot be earlier than current time.
     */
    public function testValidateWithWrongStartDateTime()
    {
        $this->entityMock->expects($this->once())->method('getName')->willReturn('Test Update');
        $startDateTime = new \DateTime('-10 minutes');
        $this->entityMock->expects($this->any())
            ->method('getStartTime')
            ->willReturn($startDateTime->format("m/d/Y H:i:s"));
        $this->model->validateCreate($this->entityMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\ValidatorException
     * @expectedExceptionMessage Future Update End Time cannot be equal or earlier than Start Time.
     */
    public function testValidateWithWrongEndDateTime()
    {
        $this->entityMock->expects($this->once())->method('getName')->willReturn('Test Update');
        $startDateTime = new \DateTime('tomorrow');
        $endDateTime = $startDateTime->sub(new \DateInterval('PT10M'));
        $this->entityMock->expects($this->any())
            ->method('getStartTime')
            ->willReturn($startDateTime->format("m/d/Y H:i:s"));
        $this->entityMock->expects($this->any())
            ->method('getEndTime')
            ->willReturn($endDateTime->format("m/d/Y H:i:s"));
        $this->model->validateCreate($this->entityMock);
    }

    public function testValidate()
    {
        $this->entityMock->expects($this->once())->method('getName')->willReturn('Test Update');
        $startDateTime = new \DateTime();
        $startDateTime->add(new \DateInterval('PT60S'));
        $this->entityMock->expects($this->any())
            ->method('getStartTime')
            ->willReturn($startDateTime->format("m/d/Y H:i:s"));
        $this->model->validateCreate($this->entityMock);
    }

    public function testValidateUpdate()
    {
        $this->entityMock->expects($this->once())->method('getName')->willReturn('Test Update');
        $startDateTime = new \DateTime();
        $this->entityMock->expects($this->any())
            ->method('getStartTime')
            ->willReturn($startDateTime->format("m/d/Y H:i:s"));

        $startDateTime->add(new \DateInterval('PT60S'));

        $this->entityMock->expects($this->any())
            ->method('getEndTime')
            ->willReturn($startDateTime->format('m/d/Y H:i:s'));
        $this->model->validateUpdate($this->entityMock);
    }

    /**
     * Scenario: End Time is less than current time. Exception expected
     *
     * @expectedExceptionMessage Future Update End Time cannot be earlier than current time.
     * @expectedException \Magento\Framework\Exception\ValidatorException
     */
    public function testValidateUpdate2()
    {
        $this->entityMock->expects($this->once())->method('getName')->willReturn('Test Update');

        $startDateTime = new \DateTime(date('m/d/Y H:i:s'));
        $startDateTime->sub(new \DateInterval('P5D'));

        $this->entityMock->expects($this->any())
            ->method('getStartTime')
            ->willReturn($startDateTime->format("m/d/Y H:i:s"));

        $startDateTime->add(new \DateInterval('P2D'));

        $this->entityMock->expects($this->any())
            ->method('getEndTime')
            ->willReturn($startDateTime->format('m/d/Y H:i:s'));
        $this->model->validateUpdate($this->entityMock);
    }
}
