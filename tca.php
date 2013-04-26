<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_spxgooglestorelocator_locations"] = array (
	"ctrl" => $TCA["tx_spxgooglestorelocator_locations"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,endtime,fe_group,storename,address,city,state,zip,country,phone,hours,url,notes,imageurl,icon,use_coordinate,categories,lat,lon"
	),
	"feInterface" => $TCA["tx_spxgooglestorelocator_locations"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		"storename" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.storename",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"address" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.address",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"city" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.city",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"state" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.state",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"zip" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.zip",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"country" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.country",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"phone" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.phone",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"hours" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.hours",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"url" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.url",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"notes" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.notes",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		"imageurl" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.imageurl",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",	
				"max_size" => 500,	
				"uploadfolder" => "uploads/tx_spxgooglestorelocator",
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"icon" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.icon",		
			"config" => Array (
				"type" => "select",
				"items" => Array (),
				"itemsProcFunc" => "tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_icon->main",
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"use_coordinate" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.use_coordinate",		
			"config" => Array (
				"type" => "check",
			)
		),
		"categories" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.categories",		
			"config" => Array (
				"type" => "select",
				"items" => Array (),
				"itemsProcFunc" => "tx_spxgooglestorelocator_tx_spxgooglestorelocator_locations_categories->main",	
				"size" => 25,	
				"maxitems" => 10,
			)
		),
		"lat" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.lat",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
		"lon" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_locations.lon",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "trim",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, storename, address, city, state, zip, country, phone, hours, url, notes, imageurl, icon, use_coordinate, categories, lat, lon")
	),
	"palettes" => array (
		"1" => array("showitem" => "endtime, fe_group")
	)
);



$TCA["tx_spxgooglestorelocator_categories"] = array (
	"ctrl" => $TCA["tx_spxgooglestorelocator_categories"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "fe_group,name,description"
	),
	"feInterface" => $TCA["tx_spxgooglestorelocator_categories"]["feInterface"],
	"columns" => array (
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		"name" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_categories.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"description" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:spx_google_storelocator/locallang_db.xml:tx_spxgooglestorelocator_categories.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "fe_group;;;;1-1-1, name, description")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);
?>
