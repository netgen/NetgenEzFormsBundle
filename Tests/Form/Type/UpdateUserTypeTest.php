<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use eZ\Publish\API\Repository\Values\User\UserUpdateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Type\UpdateUserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;
use eZ\Publish\Core\Repository\Values\User\User;

class UpdateUserTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testItExtendsAbstractType()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $this->assertInstanceOf(AbstractType::class, $updateUserType);
    }

    public function testGetName()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);

        $this->assertEquals('ezforms_update_user', $updateUserType->getName());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data must be an instance of Netgen\EzFormsBundle\Form\DataWrapper
     */
    public function testBuildFormWithoutDataWrapperMustThrowException()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $options = array('data' => 'data');

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data payload must be an instance of eZ\Publish\API\Repository\Values\User\User
     */
    public function testBuildFormDataWrapperTargetMustBeUser()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $options = array('data' => new DataWrapper('payload'));

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data payload must be an instance of eZ\Publish\API\Repository\Values\User\UserUpdateStruct
     */
    public function testBuildFormDataWrapperPayloadMustBeUserUpdateStruct()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $user = new User();

        $options = array('data' => new DataWrapper('payload', 'definition', $user));

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data definition must be an instance of eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    public function testBuildFormDataWrapperDefinitionMustBeContentType()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $userUpdateStruct = new UserUpdateStruct();
        $user = new User();

        $options = array('data' => new DataWrapper($userUpdateStruct, null, $user));

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }
}
