<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Flux_Core::registerConfigurationProvider('Tx_Fluidbackend_Provider_Configuration_StorageConfigurationProvider');

if ('BE' === TYPO3_MODE) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Backend\\Module\\ModuleLoader'] =
		array('className' => 'Tx_Fluidbackend_Override_Backend_Module_ModuleLoader');
}
