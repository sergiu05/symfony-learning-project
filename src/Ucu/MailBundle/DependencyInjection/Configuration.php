<?php

namespace Ucu\MailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ucu_mail');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
        	->children()
        		->scalarNode('subject')->defaultNull()->end()
        		->scalarNode('replyTo')->defaultNull()->end()
        		->arrayNode('from')
        			->addDefaultsIfNotSet()
        			->children()
        				->scalarNode('email')->defaultNull()->end()
        				->scalarNode('name')->defaultNull()->end()
        			->end()
        		->end()
        		->arrayNode('to')
        			->addDefaultsIfNotSet()
        			->children()
        				->scalarNode('email')->defaultNull()->end()
        				->scalarNode('name')->defaultNull()->end()
        			->end()
        		->end()
        		->arrayNode('cc')
        			->addDefaultsIfNotSet()
        			->children()
        				->scalarNode('email')->defaultNull()->end()
        				->scalarNode('name')->defaultNull()->end()
        			->end()
        		->end()
        	->end();

        return $treeBuilder;
    }
}
