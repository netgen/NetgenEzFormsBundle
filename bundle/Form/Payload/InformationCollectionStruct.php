<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Form\Payload;

final class InformationCollectionStruct
{
    /**
     * @var mixed[] An array of field values like[$fieldDefIdentifier]
     */
    private $collectedData;

    /**
     * Returns value for $fieldDefIdentifier.
     *
     * @param string $fieldDefIdentifier
     *
     * @return mixed
     */
    public function getCollectedFieldValue(string $fieldDefIdentifier)
    {
        return $this->collectedData[$fieldDefIdentifier] ?? null;
    }

    /**
     * This method returns the complete fields collection.
     */
    public function getCollectedFields(): array
    {
        return $this->collectedData;
    }

    /**
     * Sets value for $fieldDefIdentifier.
     *
     * @param string $fieldDefIdentifier
     * @param mixed $value
     */
    public function setCollectedFieldValue(string $fieldDefIdentifier, $value): void
    {
        $this->collectedData[$fieldDefIdentifier] = $value;
    }
}
