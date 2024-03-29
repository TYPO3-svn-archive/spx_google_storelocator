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
 * Class that adds the wizard icon.
 *
 * @author    Raphael Heilmann <rh@stuttgartmedia.de>
 * @package	TYPO3
 * @subpackage	tx_spxgooglestorelocator
 */
class tx_spxgooglestorelocator_pi1_wizicon {

					/**
					 * Processing the wizard items array
					 *
					 * @param	array		$wizardItems: The wizard items
					 * @return	Modified array with wizard items
					 */
					function proc($wizardItems)	{
						global $LANG;

                        $llFile = t3lib_extMgm::extPath('spx_google_storelocator').'locallang.xml';
                        $LL = t3lib_div::readLLfile($llFile, $LANG->lang);

						$wizardItems['plugins_tx_spxgooglestorelocator_pi1'] = array(
							'icon'=>t3lib_extMgm::extRelPath('spx_google_storelocator').'pi1/ce_wiz.gif',
							'title'=>$LANG->getLLL('pi1_title',$LL),
							'description'=>$LANG->getLLL('pi1_plus_wiz_description',$LL),
							'params'=>'&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=spx_google_storelocator_pi1'
						);

						return $wizardItems;
					}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/pi1/class.tx_spxgooglestorelocator_pi1_wizicon.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/spx_google_storelocator/pi1/class.tx_spxgooglestorelocator_pi1_wizicon.php']);
}

?>