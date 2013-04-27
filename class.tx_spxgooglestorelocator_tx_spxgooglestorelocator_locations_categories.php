<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2013 Raphael Heilmann <rh@stuttgartmedia.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 * Class/Function which manipulates the item-array for table/field tx_spxgooglestorelocator_locations_categories.
 *
 * @author    Raphael Heilmann <rh@stuttgartmedia.de>
 * @package    TYPO3
 * @subpackage    tx_spxgooglestorelocator
 */
class tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_categories {

    function main(&$params, &$pObj) {
        foreach ($this->getItems() as $row) {
            $params['items'][] = array($pObj->sL($row['name']), $row['name']);
        }
    }

    function getItems() {
        return ($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_spxgooglestorelocator_categories', '1=1', '', 'sorting ASC'));
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_categories.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_categories.php']);
}

?>