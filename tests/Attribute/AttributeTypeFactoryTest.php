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

namespace MetaModels\AttributeTranslatedSimpleTableBundle\Test\Attribute;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use MetaModels\AttributeTranslatedSimpleTableBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeTranslatedSimpleTableBundle\Attribute\TranslatedSimpleTable;
use MetaModels\Helper\TableManipulator;
use MetaModels\IMetaModel;
use PHPUnit\Framework\TestCase;

/**
 * Class AttributeTypeFactoryTest
 *
 * @covers \MetaModels\AttributeTranslatedSimpleTableBundle\Attribute\AttributeTypeFactory
 */
class AttributeTypeFactoryTest extends TestCase
{
    /**
     * Test the constructor.
     *
     * @return void
     *
     * @covers \MetaModels\AttributeTranslatedSimpleTableBundle\Attribute\AttributeTypeFactory::__construct
     */
    public function testConstructor(): void
    {
        $driver           = $this->getMockBuilder(Driver::class)->getMock();
        $connection       = $this->getMockBuilder(Connection::class)->setConstructorArgs([[], $driver])->getMock();
        $tableManipulator = new TableManipulator($connection, []);

        $this->assertInstanceOf(AttributeTypeFactory::class, new AttributeTypeFactory($connection, $tableManipulator));
    }

    /**
     * Test getTypeName().
     *
     * @return void
     */
    public function testTypeName(): void
    {
        $factory = $this->mockFactory();

        $this->assertSame('translatedsimpletable', $factory->getTypeName());
    }

    /**
     * Test getTypeIcon().
     *
     * @return void
     */
    public function testTypeIcon(): void
    {
        $factory = $this->mockFactory();

        $this->assertSame(
            'bundles/metamodelsattributetranslatedsimpletable/translatedtabletext.png',
            $factory->getTypeIcon()
        );
    }

    /**
     * Test create instance.
     *
     * @return void
     */
    public function testTypeClass(): void
    {
        $factory   = $this->mockFactory();
        $metaModel = $this->getMockForAbstractClass(IMetaModel::class);

        $this->assertInstanceOf(TranslatedSimpleTable::class, $factory->createInstance([], $metaModel));
    }

    /**
     * Create a factory.
     *
     * @return AttributeTypeFactory
     */
    private function mockFactory(): AttributeTypeFactory
    {
        $driver     = $this->getMockBuilder(Driver::class)->getMock();
        $connection = $this->getMockBuilder(Connection::class)->setConstructorArgs([[], $driver])->getMock();

        $factory = new AttributeTypeFactory($connection);

        return $factory;
    }
}
