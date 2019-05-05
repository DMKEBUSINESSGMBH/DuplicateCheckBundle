<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\DependencyInjection\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterAdapterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $finder = $container->getDefinition('dmk_duplicate_check.finder');

        foreach ($container->findTaggedServiceIds('dmk_duplicate_check.adapter') as $id => $tags) {
            $finder->addMethodCall('register', [new Reference($id)]);
        }
    }
}