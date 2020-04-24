<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\Type;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzFormsBundle\Form\DataWrapper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandlerRegistry;
use Netgen\Bundle\EzFormsBundle\Form\Payload\InformationCollectionStruct;
use RuntimeException;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use function array_keys;
use function in_array;

final class InformationCollectionType extends AbstractContentType
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    public function __construct(
        FieldTypeHandlerRegistry $fieldTypeHandlerRegistry,
        DataMapperInterface $dataMapper,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($fieldTypeHandlerRegistry, $dataMapper);

        $this->configResolver = $configResolver;
    }

    public function getBlockPrefix(): string
    {
        return 'ezforms_information_collection';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var DataWrapper $dataWrapper */
        $dataWrapper = $options['data'];

        if (!$dataWrapper instanceof DataWrapper) {
            throw new RuntimeException(
                'Data must be an instance of Netgen\\EzFormsBundle\\Form\\DataWrapper'
            );
        }

        /** @var InformationCollectionStruct $payload */
        $payload = $dataWrapper->payload;

        if (!$payload instanceof InformationCollectionStruct) {
            throw new RuntimeException(
                'Data payload must be an instance of Netgen\\Bundle\\EzFormsBundle\\Form\\Payload\\InformationCollectionStruct'
            );
        }

        /** @var ContentType $contentType */
        $contentType = $dataWrapper->definition;

        if (!$contentType instanceof ContentType) {
            throw new RuntimeException(
                'Data definition must be an instance of eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType'
            );
        }

        $builder->setDataMapper($this->dataMapper);

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            if ($fieldDefinition->fieldTypeIdentifier === 'ezuser') {
                continue;
            }

            if (!$fieldDefinition->isInfoCollector) {
                continue;
            }

            $handler = $this->fieldTypeHandlerRegistry->get($fieldDefinition->fieldTypeIdentifier);
            $handler->buildFieldCreateForm($builder, $fieldDefinition, $this->getLanguageCode($contentType));
        }
    }

    /**
     * If ContentType language code is in languages array then use it, else use first available one.
     */
    protected function getLanguageCode(ContentType $contentType): string
    {
        $contentTypeLanguages = array_keys($contentType->getNames());

        foreach ($this->configResolver->getParameter('languages') as $languageCode) {
            if (in_array($languageCode, $contentTypeLanguages, true)) {
                return $languageCode;
            }
        }

        return $contentType->mainLanguageCode;
    }
}
