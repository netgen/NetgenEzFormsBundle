<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\Content\Content;

class Relation extends FieldTypeHandler
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var TranslationHelper
     */
    private $translationHelper;

    /**
     * ObjectRelation constructor.
     * @param Repository $repository
     * @param TranslationHelper $translationHelper
     */
    public function __construct(Repository $repository, TranslationHelper $translationHelper)
    {
        $this->repository = $repository;
        $this->translationHelper = $translationHelper;
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    ) {
        $selectionRoot = $fieldDefinition->getFieldSettings()['selectionRoot'];

        if (empty($selectionRoot)) {
            throw new \InvalidArgumentException('SelectionRoot must be defined');
        }

        $locationService = $this->repository->getLocationService();
        $location = $locationService->loadLocation($selectionRoot);
        $locationList = $locationService->loadLocationChildren($location);

        $choices = [];
        foreach ($locationList->locations as $child) {
            /** @var Location $child */
            $choices[$this->translationHelper->getTranslatedContentNameByContentInfo($child->contentInfo)] = $child->contentInfo->id;
        }

        $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, [
            'choices' => $choices,
            'expanded' => false,
            'multiple' => false,
            'choices_as_values' => true,
        ]);
    }

    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        if (empty($value->destinationContentId)) {
            return null;
        }

        return $this->repository->getContentService()->loadContent($value->destinationContentId);
    }
}
