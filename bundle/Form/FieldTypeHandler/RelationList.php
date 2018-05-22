<?php

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use eZ\Publish\Core\Helper\TranslationHelper;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class RelationList extends FieldTypeHandler
{
    const BROWSE = 0;
    const DROPDOWN = 1;
    const LIST_RADIO = 2;
    const LIST_CHECK = 3;
    const MULTIPLE_SELECTION = 4;
    const TPLBASED_MULTI = 5;
    const TPLBASED_SINGLE = 6;

    /**
     * @var Repository
     */
    private $repository;
    
    /**
     * @var TranslationHelper
     */
    private $translationHelper;

    public function __construct(
        Repository $repository,
        TranslationHelper $translationHelper
    )
    {
        $this->repository = $repository;
        $this->translationHelper = $translationHelper;
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    )
    {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $fieldSettings = $fieldDefinition->getFieldSettings();

        $selectionMethod = $fieldSettings['selectionMethod'];

        $defaultLocation = $fieldSettings['selectionDefaultLocation'];
        $contentTypes = $fieldSettings['selectionContentTypes'];
        
        /* TODO: implement different selection methods */
        switch ($fieldSettings['selectionMethod']) {
            case self::MULTIPLE_SELECTION:
                $locationService = $this->repository->getLocationService();
                $location = $locationService->loadLocation($defaultLocation ? $defaultLocation : 2);
                $locationList = $locationService->loadLocationChildren($location);

                $choices = [];
                foreach ($locationList->locations as $child) {
                    /** @var Location $child */
                    $choices[$this->translationHelper->getTranslatedContentNameByContentInfo($child->contentInfo)] = $child->contentInfo->id;
                }

                $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, [
                    'choices' => $choices,
                    'expanded' => false,
                    'multiple' => true,
                    'choices_as_values' => true,
                ], $options);
                break;
            default:
                $locationService = $this->repository->getLocationService();
                $location = $locationService->loadLocation($defaultLocation ? $defaultLocation : 2);
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
                ], $options);
                break;
        }
    }

    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        if (empty($value->destinationContentIds)) {
            return null;
        }

        return $value->destinationContentIds;
    }

    public function convertFieldValueFromForm($data)
    {
        return new RelationListValue($data);
    }
}
