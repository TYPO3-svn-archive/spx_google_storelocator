<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages('tx_spxgooglestorelocator_locations');


t3lib_extMgm::addToInsertRecords('tx_spxgooglestorelocator_locations');


if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("spx_google_storelocator")."class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_categories.php");
if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("spx_google_storelocator")."class.tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_icon.php");

$TCA["tx_spxgooglestorelocator_locations"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations',		
		'label'     => 'storename',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'endtime' => 'endtime',	
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_spxgooglestorelocator_locations.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, endtime, fe_group, storename, address, city, state, zip, country, phone, hours, url, notes, imageurl, icon, use_coordinate, categories, lat, lon",
	)
);


t3lib_extMgm::allowTableOnStandardPages('tx_spxgooglestorelocator_categories');


t3lib_extMgm::addToInsertRecords('tx_spxgooglestorelocator_categories');

$TCA["tx_spxgooglestorelocator_categories"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_categories',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_spxgooglestorelocator_categories.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "fe_group, name, description",
	)
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:spx_google_storelocator/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_spxgooglestorelocator_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_spxgooglestorelocator_pi1_wizicon.php';

?>