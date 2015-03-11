<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

FluidTYPO3\Flux\Core::registerConfigurationProvider('FluidTYPO3\Fluidbackend\Provider\Configuration\StorageConfigurationProvider');
ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Fluid Backend: Configuration');
array_unshift($GLOBALS['TBE_MODULES']['_dispatcher'], 'FluidTYPO3\\Fluidbackend\\Override\\Backend\\Module\\ModuleRunner');

ExtensionManagementUtility::allowTableOnStandardPages('tx_fluidbackend_domain_model_configuration');
$TCA['tx_fluidbackend_domain_model_configuration'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:fluidbackend/Resources/Private/Language/locallang_db.xml:tx_fluidbackend_domain_model_configuration',
		'label'     => 'label',
		'prependAtCopy' => '',
		'hideAtCopy' => TRUE,
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'editlock' => 'editlock',
		'dividers2tabs' => TRUE,
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'default_sortby' => 'ORDER BY name ASC',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Configuration.php',
		'iconfile'          => ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif',
		'searchFields' => 'uid,name,label',
		'requestUpdate' => '',
	),
);
