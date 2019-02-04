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

use Contao\System;
use Doctrine\DBAL\Query\QueryBuilder;
use MetaModels\Attribute\Base;
use MetaModels\Attribute\IComplex;
use MetaModels\Attribute\ITranslated;
use MetaModels\IMetaModel;
use Doctrine\DBAL\Connection;

/**
 * This is the MetaModelAttribute class for handling translated table text fields.
 *
 * @package    MetaModels
 * @subpackage AttributeTranslatedSimpleTableBundle
 * @author     David Maack <david.maack@arcor.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Dziemba <adziemba@web.de>
 */
class TranslatedSimpleTable extends Base implements ITranslated, IComplex
{

    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Instantiate an MetaModel attribute.
     *
     * Note that you should not use this directly but use the factory classes to instantiate attributes.
     *
     * @param IMetaModel $objMetaModel The MetaModel instance this attribute belongs to.
     *
     * @param array      $arrData      The information array, for attribute information, refer to documentation of
     *                                 table tl_metamodel_attribute and documentation of the certain attribute classes
     *                                 for information what values are understood.
     *
     * @param Connection $connection   Database connection.
     */
    public function __construct(IMetaModel $objMetaModel, array $arrData = [], Connection $connection = null)
    {
        parent::__construct($objMetaModel, $arrData);

        if (null === $connection) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Connection is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $connection = System::getContainer()->get('database_connection');
        }

        $this->connection = $connection;
    }


    /**
     * {@inheritDoc}
     */
    public function getAttributeSettingNames()
    {
        return array_merge(
            parent::getAttributeSettingNames(),
            [
                'translatedtabletext_cols',
            ]
        );
    }

    /**
     * Retrieve the table name containing the values.
     *
     * @return string
     */
    protected function getValueTable()
    {
        return 'tl_metamodel_translatedtabletext';
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldDefinition($arrOverrides = [])
    {
        // Build DCA.
        $arrFieldDef                           = parent::getFieldDefinition($arrOverrides);
        $arrFieldDef['inputType']              = 'tableWizard';
        $arrFieldDef['eval']['allowHtml']      = true;
        $arrFieldDef['eval']['doNotSaveEmpty'] = true;
        $arrFieldDef['eval']['style']          = 'width:142px;height:66px';

        return $arrFieldDef;
    }

    /**
     * Build the where clause.
     *
     * @param QueryBuilder      $queryBuilder The query builder
     * @param array|string|null $mixIds       The item ids as array or csv list.
     * @param string|null       $strLangCode  The language code.
     * @param int|null          $intRow       The requested row.
     * @param mixed             $varCol       The requested column.
     */
    protected function buildWhere(
        QueryBuilder $queryBuilder,
        $mixIds,
        $strLangCode = null,
        $intRow = null,
        $varCol = null
    ) {
        $queryBuilder
            ->andWhere('att_id = :att_id')
            ->setParameter('att_id', (int) $this->get('id'));

        if (!empty($mixIds)) {
            if (is_array($mixIds)) {
                $queryBuilder
                    ->andWhere('item_id IN (:item_ids)')
                    ->setParameter('item_ids', $mixIds, Connection::PARAM_STR_ARRAY);
            } else {
                $queryBuilder
                    ->andWhere('item_id = :item_id')
                    ->setParameter('item_id', $mixIds);
            }
        }

        if (is_int($intRow) && is_string($varCol)) {
            $queryBuilder
                ->andWhere('row = :row AND col = :col')
                ->setParameter('row', $intRow)
                ->setParameter('col', $varCol);
        }

        if ($strLangCode) {
            $queryBuilder
                ->andWhere('langcode = :langcode')
                ->setParameter('langcode', $strLangCode);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function valueToWidget($varValue)
    {
        if (!is_array($varValue)) {
            return [];
        }

        // Get max
        $maxColumnCount = 0;
        foreach ($varValue as $row) {
            $maxColumnCount = max($maxColumnCount, count($row));
        }

        // Add missing fields and sort them.
        foreach (\array_keys($varValue) as $rowID) {
            if (count($varValue[$rowID]) < $maxColumnCount) {
                for ($i = 0; $i < $maxColumnCount; $i++) {
                    if (!isset($varValue[$rowID][$i])) {
                        $varValue[$rowID][$i] = ['value' => '', 'row' => $rowID];
                    }
                }
            }

            ksort($varValue[$rowID]);
        }

        // Rebuild the array for contao widget.
        $widgetValue = [];
        foreach ($varValue as $row) {
            foreach ($row as $key => $col) {
                $widgetValue[$col['row']][$key] = $col['value'];
            }
        }

        return $widgetValue;
    }

    /**
     * {@inheritdoc}
     */
    public function widgetToValue($varValue, $itemId)
    {
        if (!is_array($varValue)) {
            return null;
        }

        $newValue = [];
        foreach ($varValue as $k => $row) {
            foreach ($row as $kk => $col) {
                // Don't save empty strings.
                if ($col === '') {
                    continue;
                }
                $newValue[$k][$kk]['value'] = $col;
                $newValue[$k][$kk]['col']   = $kk;
                $newValue[$k][$kk]['row']   = $k;
            }
        }

        return $newValue;
    }

    /**
     * Retrieve the setter array.
     *
     * @param array  $arrCell     The cells of the table.
     *
     * @param int    $intId       The id of the item.
     *
     * @param string $strLangCode The language code.
     *
     * @return array
     */
    protected function getSetValues($arrCell, $intId, $strLangCode)
    {
        return [
            'tstamp'   => time(),
            'value'    => (string) $arrCell['value'],
            'att_id'   => $this->get('id'),
            'row'      => (int) $arrCell['row'],
            'col'      => $arrCell['col'],
            'item_id'  => $intId,
            'langcode' => $strLangCode,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslatedDataFor($arrIds, $strLangCode)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->getValueTable())
            ->orderBy('row', 'ASC')
            ->addOrderBy('col', 'ASC');

        $this->buildWhere($queryBuilder, $arrIds, $strLangCode);

        $statement = $queryBuilder->execute();
        $arrReturn = [];
        while ($value = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $arrReturn[$value['item_id']][$value['row']][$value['col']] = $value;
        }

        return $arrReturn;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function searchForInLanguages($strPattern, $arrLanguages = [])
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function setTranslatedDataFor($arrValues, $strLangCode)
    {
        // Get the ids.
        $arrIds = array_keys($arrValues);

        foreach ($arrIds as $intId) {
            // Walk every row.
            foreach ($arrValues[$intId] as $row) {
                // Walk every column and update / insert the value.
                foreach ($row as $col) {
                    $values = $this->getSetValues($col, $intId, $strLangCode);
                    if ($values['value'] === '') {
                        continue;
                    }

                    $queryBuilder = $this->connection->createQueryBuilder()->insert($this->getValueTable());
                    foreach ($values as $name => $value) {
                        $queryBuilder
                            ->setValue($name, ':' . $name)
                            ->setParameter($name, $value);
                    }

                    $sql        = $queryBuilder->getSQL();
                    $parameters = $queryBuilder->getParameters();

                    $queryBuilder = $this->connection->createQueryBuilder()->update($this->getValueTable());
                    foreach ($values as $name => $value) {
                        $queryBuilder
                            ->set($name, ':' . $name)
                            ->setParameter($name, $value);
                    }

                    $updateSql = $queryBuilder->getSQL();
                    $sql      .= ' ON DUPLICATE KEY ' . str_replace($this->getValueTable() . ' SET ', '', $updateSql);

                    $this->connection->executeQuery($sql, $parameters);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function unsetValueFor($arrIds, $strLangCode)
    {
        $queryBuilder = $this->connection->createQueryBuilder()->delete($this->getValueTable());
        $this->buildWhere($queryBuilder, $arrIds, $strLangCode);
        $queryBuilder->execute();
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getFilterOptions($idList, $usedOnly, &$arrCount = null)
    {
        return [];
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setDataFor($arrValues)
    {
        $this->setTranslatedDataFor($arrValues, $this->getMetaModel()->getActiveLanguage());
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getDataFor($arrIds)
    {
        $strActiveLanguage   = $this->getMetaModel()->getActiveLanguage();
        $strFallbackLanguage = $this->getMetaModel()->getFallbackLanguage();

        $arrReturn = $this->getTranslatedDataFor($arrIds, $strActiveLanguage);

        // Second round, fetch fallback languages if not all items could be resolved.
        if ((count($arrReturn) < count($arrIds)) && ($strActiveLanguage != $strFallbackLanguage)) {
            $arrFallbackIds = [];
            foreach ($arrIds as $intId) {
                if (empty($arrReturn[$intId])) {
                    $arrFallbackIds[] = $intId;
                }
            }

            if ($arrFallbackIds) {
                $arrFallbackData = $this->getTranslatedDataFor($arrFallbackIds, $strFallbackLanguage);
                // Cannot use array_merge here as it would renumber the keys.
                foreach ($arrFallbackData as $intId => $arrValue) {
                    $arrReturn[$intId] = $arrValue;
                }
            }
        }

        return $arrReturn;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \RuntimeException When the passed value is not an array of ids.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function unsetDataFor($arrIds)
    {
        if (!is_array($arrIds)) {
            throw new \RuntimeException(
                'TranslatedSimpleTable::unsetDataFor() invalid parameter given! Array of ids is needed.',
                1
            );
        }

        if (empty($arrIds)) {
            return;
        }

        $queryBuilder = $this->connection->createQueryBuilder()->delete($this->getValueTable());
        $this->buildWhere($queryBuilder, $arrIds);
        $queryBuilder->execute();
    }
}
