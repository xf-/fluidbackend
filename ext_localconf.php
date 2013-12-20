<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

FluidTYPO3\Flux\Core::registerConfigurationProvider('FluidTYPO3\Fluidbackend\Provider\Configuration\StorageConfigurationProvider');

if ('BE' === TYPO3_MODE) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Backend\\Module\\ModuleLoader'] =
		array('className' => 'FluidTYPO3\Fluidbackend\Override\Backend\Module\ModuleLoader');
}
