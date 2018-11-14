<?php

/**
 * This file is part of MetaModels/attribute_attributetranslatedsimpletable.
 *
 * (c) 2018 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedSimpleTableBundle
 * @author     Andreas Dziemba <dziemba@men-at-work.de>
 * @copyright  2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_attributetranslatedsimpletable/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeTranslatedSimpleTableBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class AttributeTranslatedSimpleTableExtension
 *
 * @package MetaModels\AttributeTranslatedSimpleTableBundle\DependencyInjection
 */
class MetaModelsAttributeTranslatedSimpleTableExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('listeners.yml');
    }
}