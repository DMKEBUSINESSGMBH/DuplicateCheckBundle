<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Provider;

use Doctrine\ORM\Mapping\ClassMetadata;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;

class ConfigProvider
{
    private const SCOPE = 'duplicate';

    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * ConfigProvider constructor.
     *
     * @param DoctrineHelper $doctrineHelper
     * @param ConfigManager  $configManager
     */
    public function __construct(DoctrineHelper $doctrineHelper, ConfigManager $configManager)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->configManager = $configManager;
    }

    /**
     * Checks if the given class is enabeld for the duplicate check.
     *
     * @param string $class
     *
     * @return bool
     */
    public function isEntityEnabled(string $class): bool
    {
        if (!$this->configManager->hasConfig($class)) {
            return false;
        }

        return $this->configManager->getEntityConfig(self::SCOPE, $class)->is('enabled');
    }

    /**
     * Returns true if the field should be used for the duplicate check.
     *
     * @param string $class
     * @param string $field
     *
     * @return bool
     */
    public function isFieldEnabled(string $class, string $field): bool
    {
        if (!$this->configManager->hasConfig($class, $field)) {
            return false;
        }

        return $this->configManager->getFieldConfig(self::SCOPE, $class, $field)->is('enabled');
    }

    /**
     * @param string $class
     *
     * @return array
     */
    public function getEnabledFields(string $class): array
    {
        $metadata = $this->doctrineHelper->getEntityMetadataForClass($class);

        if($metadata instanceof ClassMetadata) {
            return array_filter($metadata->getFieldNames(), function (string $name) use ($class) {
                return $this->isFieldEnabled($class, $name);
            });
        }

        return [];
    }

    /**
     * Returns all FQCN for all enabled entities.
     *
     * @return string[]
     */
    public function getAllEnabledEntities(): iterable
    {
        $configs = $this->configManager->getConfigs(self::SCOPE);

        foreach ($configs as $config) {
            if (!$config->is('enabeld')) {
                continue;
            }

            yield $config->getId()->getClassName();
        }
    }
}
