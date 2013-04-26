<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$spx_google_storelocator_conf = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['spx_google_storelocator']);

t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_spxgooglestorelocator_locations=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_spxgooglestorelocator_categories=1
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_spxgooglestorelocator_pi1 = < plugin.tx_spxgooglestorelocator_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_spxgooglestorelocator_pi1.php','_pi1','list_type',1);
?>