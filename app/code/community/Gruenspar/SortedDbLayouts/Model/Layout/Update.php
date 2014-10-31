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
 * Layout update model.
 *
 * @category Gruenspar
 * @package  Gruenspar_SortedDbLayouts
 * @author   Jan Papenbrock <j.papenbrock@gruenspar.de>
 * @license  MIT
 */
class Gruenspar_SortedDbLayouts_Model_Layout_Update extends Mage_Core_Model_Layout_Update
{
    /**
     * Add layout update in a sort order.
     *
     * @param string $update    Update XML string.
     * @param int    $sortOrder Sort order
     *
     * @return $this
     */
    public function addSortedUpdate($update, $sortOrder)
    {
        $veryLargeIndex = 1000000;
        $sortOrder      = $sortOrder * 100; // allows 100 elements with the same sort order

        $index = $veryLargeIndex + $sortOrder - 1;

        do {
            $index++;
        } while (isset($this->_updates[$index]));


        $this->_updates[$index] = $update;

        return $this;
    }

    /**
     * Fetch layout updates from DB for given handle.
     *
     * @param string $handle Handle string.
     *
     * @return bool
     */
    public function fetchDbLayoutUpdates($handle)
    {
        $_profilerKey = 'layout/db_update: '.$handle;
        Varien_Profiler::start($_profilerKey);
        $updates = $this->_getUpdates($handle);
        if (!count($updates)) {
            return false;
        }

        foreach ($updates as $update) {
            $updateStr = $update['xml'];
            $sortOrder = $update['sort_order'];

            $updateStr = '<update_xml>' . $updateStr . '</update_xml>';
            $updateStr = str_replace($this->_subst['from'], $this->_subst['to'], $updateStr);
            $updateXml = simplexml_load_string($updateStr, $this->getElementClass());
            $this->fetchRecursiveUpdates($updateXml);
            $this->addSortedUpdate($updateXml->innerXml(), $sortOrder);
        }

        ksort($this->_updates);

        Varien_Profiler::stop($_profilerKey);
        return true;
    }

    /**
     * Get updates by handle
     *
     * @param string $handle
     *
     * @return array
     */
    protected function _getUpdates($handle)
    {
        return Mage::getResourceModel('core/layout')->fetchUpdatesByHandle($handle);
    }

    /**
     * Get update string
     *
     * @deprecated
     *
     * @param string $handle
     *
     * @return string
     */
    protected function _getUpdateString($handle)
    {
        $updates = $this->_getUpdates($handle);
        $result = array();

        foreach ($updates as $update) {
            $result[] = $update['xml'];
        }

        return implode("", $result);
    }
}
