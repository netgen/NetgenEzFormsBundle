<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzFormsBundle\Tests\Stubs;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

final class ConfigResolverStub implements ConfigResolverInterface
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @var string
     */
    private $defaultNamespace = 'ibexa.site_access.config';

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getParameter(string $paramName, ?string $namespace = null, ?string $scope = null)
    {
        return $this->parameters[$namespace ?? $this->defaultNamespace][$paramName] ?? null;
    }

    public function hasParameter(string $paramName, ?string $namespace = null, ?string $scope = null): bool
    {
        return isset($this->parameters[$namespace ?? $this->defaultNamespace][$paramName]);
    }

    public function setDefaultNamespace(string $defaultNamespace): void
    {
        $this->defaultNamespace = $defaultNamespace;
    }

    public function getDefaultNamespace(): string
    {
        return $this->defaultNamespace;
    }
}
