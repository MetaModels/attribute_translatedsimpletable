<?php

/**
 * This file is part of MetaModels/attribute_translatedsimpletable.
 *
 * (c) 2012-2019 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedTableText
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtabletext/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeTranslatedSimpleTableBundle\Test;

use MetaModels\AttributeTranslatedSimpleTableBundle\DependencyInjection\MetaModelsAttributeTranslatedSimpleTableExtension;
use MetaModels\AttributeTranslatedSimpleTableBundle\MetaModelsAttributeTranslatedSimpleTableBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\ComposerResource;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MetaModelsAttributeTranslatedSimpleTableBundleTest
 *
 * @covers  \MetaModels\AttributeTranslatedSimpleTableBundle\MetaModelsAttributeTranslatedSimpleTableBundle
 */
class MetaModelsAttributeTranslatedSimpleTableBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new MetaModelsAttributeTranslatedSimpleTableBundle();

        $this->assertInstanceOf(MetaModelsAttributeTranslatedSimpleTableBundle::class, $bundle);
    }

    public function testReturnsTheContainerExtension(): void
    {
        $extension = (new MetaModelsAttributeTranslatedSimpleTableBundle())->getContainerExtension();

        $this->assertInstanceOf(MetaModelsAttributeTranslatedSimpleTableExtension::class, $extension);
    }

    /**
     * @covers \MetaModels\AttributeTranslatedSimpleTableBundle\DependencyInjection\MetaModelsAttributeTranslatedSimpleTableExtension::load
     */
    public function testLoadExtensionConfiguration(): void
    {
        $extension = (new MetaModelsAttributeTranslatedSimpleTableBundle())->getContainerExtension();
        $container = new ContainerBuilder();

        $extension->load([], $container);

        $this->assertInstanceOf(ComposerResource::class, $container->getResources()[0]);
        $this->assertInstanceOf(FileResource::class, $container->getResources()[1]);
        $this->assertSame(
            \dirname(__DIR__) . '/src/Resources/config/services.yml',
            $container->getResources()[1]->getResource()
        );
    }
}
