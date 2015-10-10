<?php
/**
 * Clickatell Symfony Bundle
 *
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @link     https://github.com/arcturial
 */

namespace Clickatell\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Register the container services.
 *
 * @package  Clickatell\Symfony\DependencyInjection
 * @author   Chris Brand <chris@cainsvault.com>
 * @link     https://github.com/arcturial
 */
class ClickatellExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($configs);
        $config = $this->processConfiguration($configuration, $configs);

        $container
            ->register('clickatell', $config['class'])
            ->setArguments($config['arguments']);
    }
}
