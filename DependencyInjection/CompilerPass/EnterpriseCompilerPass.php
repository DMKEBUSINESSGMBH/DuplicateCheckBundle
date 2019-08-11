<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EnterpriseCompilerPass implements CompilerPassInterface
{
    private const SERVICE_IDS = [
        // To be filled.
    ];

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        foreach (self::SERVICE_IDS as $id) {
            $container->findDefinition($id)->addTag('dmk_duplicate_check.adapter');
        }
    }
}
