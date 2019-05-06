<?php

namespace DMK\DuplicateCheckBundle\Placeholder;

use DMK\DuplicateCheckBundle\Provider\ConfigProvider;
use Doctrine\Common\Util\ClassUtils;

class DuplicateFilter
{
    private $config;

    public function __construct(ConfigProvider $provider)
    {
        $this->config = $provider;
    }

    public function isCheckEnabled($entity)
    {
        $class = ClassUtils::getClass($entity);

        return true;
        return $this->config->isEntityEnabled($class);
    }
}
