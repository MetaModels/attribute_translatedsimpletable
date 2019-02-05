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

namespace MetaModels\AttributeTranslatedSimpleTableBundle\Test\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use MetaModels\AttributeTranslatedSimpleTableBundle\ContaoManager\Plugin;
use MetaModels\AttributeTranslatedSimpleTableBundle\MetaModelsAttributeTranslatedSimpleTableBundle;
use MetaModels\CoreBundle\MetaModelsCoreBundle;
use MetaModels\FilterPerimetersearchBundle\MetaModelsFilterPerimetersearchBundle;
use PHPUnit\Framework\TestCase;

/**
 * Class PluginTest
 *
 * @covers \MetaModels\AttributeTranslatedSimpleTableBundle\ContaoManager\Plugin
 */
class PluginTest extends TestCase
{
    /**
     * Test get bundles.
     *
     * @covers \MetaModels\AttributeTranslatedSimpleTableBundle\ContaoManager\Plugin::getBundles
     */
    public function testGetBundles()
    {
        $plugin = new Plugin();
        $parser = $this->getMockBuilder(ParserInterface::class)->getMock();

        $bundleConfig = BundleConfig::create(MetaModelsAttributeTranslatedSimpleTableBundle::class)
            ->setLoadAfter(
                [
                    ContaoCoreBundle::class,
                    MetaModelsCoreBundle::class,
                ]
            );

        $this->assertArraySubset($plugin->getBundles($parser), [$bundleConfig]);
    }
}
