<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Datagrid;


use DMK\DuplicateCheckBundle\Provider\ConfigProvider;
use Oro\Bundle\DataGridBundle\Datasource\ResultRecord;
use Oro\Bundle\EntityBundle\Provider\EntityClassNameProviderInterface;

final class EntityTypeProvider
{
    private $entityClassNameProvider;

    private $configProvider;

    public function __construct(EntityClassNameProviderInterface $provider, ConfigProvider $configProvider)
    {
        $this->entityClassNameProvider = $provider;
        $this->configProvider = $configProvider;
    }

    /**
     * @param string $gridName
     * @param string $keyName
     * @param array  $node
     *
     * @return callable
     */
    public function getEntityType($gridName, $keyName, $node)
    {
        return function (ResultRecord $record) {
            return $this->entityClassNameProvider->getEntityClassName(
                $record->getValue('class')
            );
        };
    }

    /**
     * @return array [entity class => entity type, ...]
     */
    public function getEntityTypes()
    {
        $result = [];
        $classNames = $this->configProvider->getAllEnabledEntities();
        foreach ($classNames as $className) {
            $label = $this->entityClassNameProvider->getEntityClassName($className);
            if ($label) {
                $result[$label] = $className;
            }
        }
        asort($result, SORT_STRING | SORT_FLAG_CASE);

        return $result;
    }
}
