<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\FieldType\Value;
use InvalidArgumentException;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class Relation extends FieldTypeHandler
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function convertFieldValueToForm(Value $value, ?FieldDefinition $fieldDefinition = null)
    {
        if (empty($value->destinationContentId)) {
            return null;
        }

        return $value->destinationContentId;
    }

    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        ?Content $content = null
    ): void {
        $selectionRoot = $fieldDefinition->getFieldSettings()['selectionRoot'];

        if (empty($selectionRoot)) {
            throw new InvalidArgumentException('SelectionRoot must be defined');
        }

        $locationService = $this->repository->getLocationService();
        $location = $locationService->loadLocation($selectionRoot);
        $locationList = $locationService->loadLocationChildren($location);

        $choices = [];
        foreach ($locationList->locations as $child) {
            /* @var Location $child */
            $choices[$child->getContent()->getName()] = $child->contentInfo->id;
        }

        $formBuilder->add($fieldDefinition->identifier, ChoiceType::class, [
            'choices' => $choices,
            'expanded' => false,
            'multiple' => false,
        ]);
    }
}
