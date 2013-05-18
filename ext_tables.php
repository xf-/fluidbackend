<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Flux_Core::registerConfigurationProvider('Tx_Fluidbackend_Provider_Configuration_StorageConfigurationProvider');
t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Fluid Backend: Configuration');

t3lib_extMgm::allowTableOnStandardPages('tx_fluidbackend_domain_model_configuration');
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Configuration.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif',
		'searchFields' => 'uid,name,label',
		'requestUpdate' => '',
	),
);