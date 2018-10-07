<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter\DBAL;


use DMK\DuplicateCheckBundle\Adapter\AdapterInterface;
use DMK\DuplicateCheckBundle\Factory\FactoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;

class SoundexAdapter implements AdapterInterface
{
    private $registry;

    private $factory;
    private $config;

    public function __construct(ManagerRegistry $registry, ConfigProvider $config, FactoryInterface $factory)
    {
        $this->registry = $registry;
        $this->factory = $factory;
        $this->config = $config;
    }

    public function process($object): iterable
    {

    }

    public function support($object): bool
    {
        try {
            /** @var EntityManager $em */
            $em = $this->registry->getManagerForClass(ClassUtils::getClass($object));
            $em->getConnection()->exec('SELECT SOUNDEX(1)');

            $config = $this->config->getConfig(ClassUtils::getClass($object), 'duplicate');

            return $config['enabled'] ?? false;
        } catch (\PDOException $e) {
            return false;
        }
    }
}