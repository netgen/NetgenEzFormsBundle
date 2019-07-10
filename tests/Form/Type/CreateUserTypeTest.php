<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Type;

use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\User\UserCreateStruct;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Type\CreateUserType;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilder;

class CreateUserTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $updateUserType = new CreateUserType($handlerRegistry, $dataMapper);
        self::assertInstanceOf(AbstractType::class, $updateUserType);
    }

    public function testBuildFormWithoutDataWrapperMustThrowException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data must be an instance of Netgen\EzFormsBundle\Form\DataWrapper');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $options = ['data' => 'data'];

        $updateUserType = new CreateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildFormDataWrapperTargetMustBeUser(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data payload must be an instance of eZ\Publish\API\Repository\Values\User\User');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $options = ['data' => new DataWrapper('payload')];

        $updateUserType = new CreateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }

    public function testBuildForm(): void
    {
        $fieldTypeHandler = $this->getMockBuilder(FieldTypeHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildFieldCreateForm'])
            ->getMockForAbstractClass();

        $fieldTypeHandler->expects(self::once())
            ->method('buildFieldCreateForm');

        $handlerRegistry = $this->getMockBuilder(FieldTypeHandlerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $handlerRegistry->expects(self::once())
            ->method('get')
            ->willReturn($fieldTypeHandler);

        $dataMapper = $this->getMockForAbstractClass(DataMapperInterface::class);

        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setDataMapper'])
            ->getMock();

        $contentType = new ContentType(
            [
                'id' => 123,
                'fieldDefinitions' => [
                    new FieldDefinition(
                        [
                            'id' => 'id',
                            'identifier' => 'identifier',
                        ]
                    ),
                ],
            ]
        );
        $userUpdateStruct = new UserCreateStruct(['contentType' => $contentType]);

        $options = ['data' => new DataWrapper($userUpdateStruct)];

        $updateUserType = new CreateUserType($handlerRegistry, $dataMapper);
        $updateUserType->buildForm($formBuilder, $options);
    }
}
