<?php
/**
 * Clickatell Symfony Bundle
 *
 * @package  Clickatell
 * @author   Chris Brand <chris@cainsvault.com>
 * @link     https://github.com/arcturial
 */

namespace Clickatell\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Symfony configuration. Defines the values required in the
 * config file.
 *
 * @package  Clickatell\Symfony\DependencyInjection
 * @author   Chris Brand <chris@cainsvault.com>
 * @link     https://github.com/arcturial
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder
            ->root('clickatell')
                ->children()
                    ->scalarNode('class')
                        ->cannotBeEmpty()
                        ->defaultValue('Clickatell\Api\ClickatellHttp')
                        ->validate()
                            ->ifTrue(function ($v) { return !class_exists($v, true) || !is_subclass_of($v, "Clickatell\Clickatell"); })
                            ->thenInvalid('The class %s must be of type Clickatell\Clickatell.')
                        ->end()
                    ->end()
                    ->arrayNode('arguments')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->prototype('scalar')
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
