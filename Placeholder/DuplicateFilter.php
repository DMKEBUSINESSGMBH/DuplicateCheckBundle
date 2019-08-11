<?php

namespace DMK\DuplicateCheckBundle\Placeholder;

use DMK\DuplicateCheckBundle\Provider\ConfigProvider;

class DuplicateFilter
{
    /**
     * @var ConfigProvider
     */
    private $config;

    public function __construct(ConfigProvider $provider)
    {
        $this->config = $provider;
    }

    /**
     * @param object $entity
     *
     * @return bool
     */
    public function isCheckEnabled($entity): bool
    {
        return $this->config->isEntityEnabled(get_class($entity));
    }
}
