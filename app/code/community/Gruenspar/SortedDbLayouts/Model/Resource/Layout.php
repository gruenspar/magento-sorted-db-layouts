<?php

/**
 * This file is part of Gruenspar_SortedDbLayouts.
 *
 * PHP version 5
 *
 * @category  Gruenspar
 * @package   Gruenspar_SortedDbLayouts
 * @author    Jan Papenbrock <j.papenbrock@gruenspar.de>
 * @copyright 2014 Gruenspar IT (http://www.gruenspar.de)
 * @license   MIT
 * @link      http://www.gruenspar.de
 * @since     1.0.0
 */

/**
 * Layout resource model.
 *
 * @category Gruenspar
 * @package  Gruenspar_SortedDbLayouts
 * @author   Jan Papenbrock <j.papenbrock@gruenspar.de>
 * @license  MIT
 */
class Gruenspar_SortedDbLayouts_Model_Resource_Layout extends Mage_Core_Model_Resource_Layout
{

    /**
     * Retrieve layout updates by handle.
     *
     * Overridden to load layout updates for all templates in the design
     * hierarchy, not only for exact template match.
     *
     * @param string $handle Handle.
     * @param array  $params Params.
     *
     * @return string
     */
    public function fetchUpdatesByHandle($handle, $params = array())
    {
        $bind = array(
            'store_id'  => Mage::app()->getStore()->getId(),
            'area'      => Mage::getSingleton('core/design_package')->getArea(),
            'package'   => Mage::getSingleton('core/design_package')->getPackageName(),
            'theme'     => Mage::getSingleton('core/design_package')->getTheme('layout')
        );

        foreach ($params as $key => $value) {
            if (isset($bind[$key])) {
                $bind[$key] = $value;
            }
        }
        $bind['layout_update_handle'] = $handle;
        $result = '';

        $readAdapter = $this->_getReadAdapter();
        if ($readAdapter) {
            $select = $readAdapter->select()
                ->from(
                    array('layout_update' => $this->getMainTable()),
                    array($this->getIdFieldName(), 'xml', 'sort_order')
                )
                ->join(
                    array(
                        'link'=>$this->getTable('core/layout_link')
                    ),
                    'link.layout_update_id=layout_update.layout_update_id',
                    ''
                )
                ->where('link.store_id IN (0, :store_id)')
                ->where('link.area = :area')
                ->where('link.package = :package')
                ->where('link.theme = :theme')
                ->where('layout_update.handle = :layout_update_handle')
                ->order('layout_update.sort_order ' . Varien_Db_Select::SQL_ASC);

            $result = $readAdapter->fetchAssoc($select, $bind);
        }
        return $result;
    }
}
