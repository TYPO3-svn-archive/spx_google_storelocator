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
 * Class/Function which manipulates the item-array for table/field tx_spxgooglestorelocator_locations_categories.
 *
 * @author	Raphael Heilmann <rapha@dmozed.org>
 * @package	TYPO3
 * @subpackage	tx_spxgooglestorelocator
 */
class tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_categories {
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
									$params['items'][] = array($pObj->sL($row['name']), $row['name']);
								}

								// No return - the $params and $pObj variables are passed by reference, so just change content in then and it is passed back automatically...
							}

/**

 * function.

 */

							function getItems() {


								$query = "SELECT * FROM  tx_spxgooglestorelocator_categories WHERE 1 ORDER BY sorting";

								$res = mysql(TYPO3_db,$query);

								$out=array();

								while($row = mysql_fetch_assoc($res)){

									$out[]=$row;

								}

								return $out;

							}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_categories.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_categories.php']);
}

?>
