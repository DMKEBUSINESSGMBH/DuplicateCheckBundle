<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle;

use DMK\DuplicateCheckBundle\DependencyInjection\CompilerPass\EnterpriseCompilerPass;
use DMK\DuplicateCheckBundle\DependencyInjection\CompilerPass\RegisterAdapterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Oro\Bundle\PlatformProBundle\OroPlatformProBundle;

class DMKDuplicateCheckBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        if (static::isEnterpriseEdition()) {
            $container->addCompilerPass(new EnterpriseCompilerPass());
        }

        $container->addCompilerPass(new RegisterAdapterCompilerPass());
    }

    public static function isEnterpriseEdition(): bool
    {
        return class_exists(OroPlatformProBundle::class);
    }
}