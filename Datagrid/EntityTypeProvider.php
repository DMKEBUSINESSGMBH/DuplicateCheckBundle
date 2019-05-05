<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Datagrid;


use Oro\Bundle\EntityBundle\Provider\EntityClassNameProviderInterface;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;

final class EntityTypeProvider
{
    private $entityClassNameProvider;

    private $configProvider;

    public function __construct(EntityClassNameProviderInterface $provider, ConfigProvider $configProvider)
    {
        $this->entityClassNameProvider = $provider;
        $this->configProvider = $configProvider;
    }

    public function getEntityTypes()
    {
        $result = [];

    }
}