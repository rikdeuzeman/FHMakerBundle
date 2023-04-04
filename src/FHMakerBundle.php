<?php

declare(strict_types=1);

namespace FH\Bundle\MakerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FHMakerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
