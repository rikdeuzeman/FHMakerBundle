<?php

declare(strict_types=1);

namespace FH\Bundle\MakerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class FHMakerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $fileLoader = (new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config')));

        $fileLoader->load('maker.yaml');
    }
}
