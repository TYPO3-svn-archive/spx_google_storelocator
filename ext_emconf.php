<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "spx_google_storelocator".
 *
 * Auto generated 30-04-2013 10:18
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Google Store Locator',
	'description' => 'Manage store locations, search by distance and show them using Google Maps API v3',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.1.0',
	'dependencies' => 'static_info_tables,static_info_tables_de',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Raphael Heilmann',
	'author_email' => 'rh@stuttgartmedia.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'static_info_tables' => '0.0.0',
			'static_info_tables_de' => '0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:29:{s:9:"ChangeLog";s:4:"b642";s:80:"class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_categories.php";s:4:"72e1";s:74:"class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_icon.php";s:4:"d55d";s:21:"ext_conf_template.txt";s:4:"d1af";s:12:"ext_icon.gif";s:4:"037b";s:17:"ext_localconf.php";s:4:"5918";s:14:"ext_tables.php";s:4:"04a1";s:14:"ext_tables.sql";s:4:"7420";s:44:"icon_tx_spxgooglestorelocator_categories.gif";s:4:"4fe7";s:43:"icon_tx_spxgooglestorelocator_locations.gif";s:4:"4fe7";s:13:"locallang.xml";s:4:"506d";s:16:"locallang_db.xml";s:4:"3d7d";s:6:"map.js";s:4:"67e5";s:10:"README.txt";s:4:"1ab3";s:7:"tca.php";s:4:"3672";s:20:"doc/circle_green.gif";s:4:"760f";s:14:"doc/manual.sxw";s:4:"ca7c";s:18:"doc/star_green.gif";s:4:"8ee6";s:23:"doc/starlarge_black.gif";s:4:"d239";s:19:"doc/wizard_form.dat";s:4:"29f2";s:20:"doc/wizard_form.html";s:4:"9d0b";s:14:"pi1/ce_wiz.gif";s:4:"aec2";s:42:"pi1/class.tx_spxgooglestorelocator_pi1.php";s:4:"acc9";s:50:"pi1/class.tx_spxgooglestorelocator_pi1_wizicon.php";s:4:"345e";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"4a22";s:24:"pi1/static/editorcfg.txt";s:4:"6e54";s:20:"static/constants.txt";s:4:"137b";s:16:"static/setup.txt";s:4:"33fd";}',
	'suggests' => array(
	),
);

?>