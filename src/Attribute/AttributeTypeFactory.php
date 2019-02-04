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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_translatedtabletext/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeTranslatedSimpleTableBundle\Attribute;

use Doctrine\DBAL\Connection;
use MetaModels\Attribute\AbstractAttributeTypeFactory;

/**
 * Attribute type factory for translated simple table attributes.
 */
class AttributeTypeFactory extends AbstractAttributeTypeFactory
{

    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Create a new instance.
     *
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->typeName   = 'translatedsimpletable';
        $this->typeIcon   = 'bundles/metamodelsattributetranslatedsimpletable/translatedtabletext.png';
        $this->typeClass  = TranslatedSimpleTable::class;
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $metaModel)
    {
        return new $this->typeClass($metaModel, $information, $this->connection);
    }
}
