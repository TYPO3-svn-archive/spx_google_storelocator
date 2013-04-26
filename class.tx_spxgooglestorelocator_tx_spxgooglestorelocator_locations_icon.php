<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Raphael Heilmann <rapha@dmozed.org>
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
 * Class/Function which manipulates the item-array for table/field tx_spxgooglestorelocator_locations_icon.
 *
 * @author	Raphael Heilmann <rapha@dmozed.org>
 * @package	TYPO3
 * @subpackage	tx_spxgooglestorelocator
 */
class tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_icon {
							function main(&$params,&$pObj)	{
/*								
								debug('Hello World!',1);
								debug('$params:',1);
								debug($params);
								debug('$pObj:',1);
								debug($pObj);
*/
								$rows=$this->getItems();

              							while(list($c,$row)=each($rows)){
									$params['items'][] = array($pObj->sL($row), $row);
								}

								// No return - the $params and $pObj variables are passed by reference, so just change content in then and it is passed back automatically...
							}

/**

 * function.

 */

							function getItems() {


								if ($handle = opendir(PATH_site.'uploads/tx_spxgooglestorelocator/icons')) {

    									while (false !== ($file = readdir($handle))) {

										    if (substr($file,0,1)!='.')
                          $out[]=$file;
									}

								}

								return $out;
    								closedir($handle);

							}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_icon.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_icon.php']);
}

?>
