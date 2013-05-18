<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_fluidbackend_domain_model_configuration'] = array(
	'ctrl' => $TCA['tx_fluidbackend_domain_model_configuration']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'cruser_id,pid,sys_language_uid,l10n_parent,l10n_diffsource,hidden,name,label,configuration'
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name,label,configuration'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'feInterface' => $TCA['tx_fluidbackend_domain_model_configuration']['feInterface'],
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cms/locallang_ttc.xml:sys_language_uid_formlabel',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_fluidbackend_domain_model_configuration',
				'foreign_table_where' => 'AND tx_fluidbackend_domain_model_configuration.pid=###CURRENT_PID### AND tx_fluidbackend_domain_model_configuration.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),
		'cruser_id' => array(
			'label' => 'cruser_id',
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'pid' => array(
			'label' => 'pid',
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'crdate' => array(
			'label' => 'crdate',
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'tstamp' => array(
			'label' => 'crdate',
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:fluidbackend/Resources/Private/Language/locallang_db.xml:tx_fluidbackend_domain_model_configuration.name',
			'config' => array(
				'type' => 'none',
				'size' => 64,
				'eval' => 'trim',
			)
		),
		'label' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:fluidbackend/Resources/Private/Language/locallang_db.xml:tx_fluidbackend_domain_model_configuration.label',
			'config' => array(
				'type' => 'input',
				'size' => 64,
				'eval' => 'trim',
			)
		),
		'configuration' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:fluidbackend/Resources/Private/Language/locallang_db.xml:tx_fluidbackend_domain_model_configuration.configuration',
			'config' => array(
				'type' => 'flex',
			)
		),
	),
);
