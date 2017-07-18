<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use eZ\Publish\API\Repository\Values\User\UserUpdateStruct;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\User\User;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Type\UpdateUserType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

class UpdateUserTypeTest extends TestCase
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Data definition (ContentType) does not correspond to the data target (Content)
     */
    public function testBuildFormContentTypeIdsMustMatch()
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setDataMapper'))
            ->getMock();

        $contentInfo = new ContentInfo(array('contentTypeId' => 123));
        $versionInfo = new VersionInfo(array('contentInfo' => $contentInfo));
        $content = new Content(array('versionInfo' => $versionInfo));
        $user = new User(array('content' => $content));

        $userUpdateStruct = new UserUpdateStruct();

        $contentType = new ContentType(
            array(
                'id' => 654,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'identifier',
                        )
                    ),
                ),
            )
        );

        $options = array('data' => new DataWrapper($userUpdateStruct, $contentType, $user));

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildForm()
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array('buildFieldUpdateForm'))
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects($this->once())
            ->method('buildFieldUpdateForm');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $handlerRegistry->expects($this->once())
            ->willReturn($fieldTypeHandler)
            ->method('get');

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setDataMapper'))
            ->getMock();

        $contentInfo = new ContentInfo(array('contentTypeId' => 123));
        $versionInfo = new VersionInfo(array('contentInfo' => $contentInfo));
        $content = new Content(array('versionInfo' => $versionInfo));
        $user = new User(array('content' => $content));

        $contentUpdateStruct = new ContentUpdateStruct(
            array(
                'initialLanguageCode' => 'eng-GB',
            )
        );
        $userUpdateStruct = new UserUpdateStruct(array('contentUpdateStruct' => $contentUpdateStruct));

        $contentType = new ContentType(
            array(
                'id' => 123,
                'fieldDefinitions' => array(
                    new FieldDefinition(
                        array(
                            'id' => 'id',
                            'identifier' => 'identifier',
                        )
                    ),
                ),
            )
        );

        $options = array('data' => new DataWrapper($userUpdateStruct, $contentType, $user));

        $updateUserType = new UpdateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }
}
