<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "fluidbackend".
 *
 * Auto generated 03-06-2013 20:15
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Fluid Backend Engine',
	'description' => 'Fluid Backend engine - create easy backend modules based on Flux forms',
	'category' => 'be',
	'author' => 'Claus Due',
	'author_email' => 'claus@wildside.dk',
	'author_company' => 'Wildside A/S',
	'shy' => '',
	'dependencies' => 'cms,flux',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'version' => '0.9.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5-0.0.0',
			'cms' => '',
			'flux' => '5.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:33:{s:12:"ext_icon.gif";s:4:"68b4";s:17:"ext_localconf.php";s:4:"369c";s:14:"ext_tables.php";s:4:"a582";s:14:"ext_tables.sql";s:4:"7c54";s:9:"README.md";s:4:"e529";s:48:"Classes/Controller/AbstractBackendController.php";s:4:"c96e";s:40:"Classes/Controller/BackendController.php";s:4:"c04f";s:38:"Classes/Domain/Model/Configuration.php";s:4:"0247";s:53:"Classes/Domain/Repository/ConfigurationRepository.php";s:4:"2ca9";s:33:"Classes/Outlet/AbstractOutlet.php";s:4:"b841";s:35:"Classes/Outlet/ControllerOutlet.php";s:4:"8e50";s:37:"Classes/Outlet/FlashMessageOutlet.php";s:4:"bc37";s:29:"Classes/Outlet/JsonOutlet.php";s:4:"e398";s:34:"Classes/Outlet/OutletInterface.php";s:4:"284e";s:35:"Classes/Outlet/TypoScriptOutlet.php";s:4:"014a";s:48:"Classes/Override/Backend/Module/ModuleLoader.php";s:4:"9b37";s:63:"Classes/Provider/Configuration/StorageConfigurationProvider.php";s:4:"97db";s:40:"Classes/Service/ConfigurationService.php";s:4:"7611";s:33:"Classes/Service/OutletService.php";s:4:"39ae";s:38:"Classes/ViewHelpers/FormViewHelper.php";s:4:"d9e2";s:40:"Classes/ViewHelpers/ModuleViewHelper.php";s:4:"622f";s:55:"Classes/ViewHelpers/Outlet/AbstractOutletViewHelper.php";s:4:"9453";s:51:"Classes/ViewHelpers/Outlet/ControllerViewHelper.php";s:4:"2b72";s:53:"Classes/ViewHelpers/Outlet/FlashMessageViewHelper.php";s:4:"5db3";s:45:"Classes/ViewHelpers/Outlet/JsonViewHelper.php";s:4:"8331";s:51:"Classes/ViewHelpers/Outlet/TypoScriptViewHelper.php";s:4:"c140";s:35:"Configuration/TCA/Configuration.php";s:4:"6f96";s:34:"Configuration/TypoScript/setup.txt";s:4:"2d73";s:40:"Resources/Private/Language/locallang.xml";s:4:"d81b";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"fcdf";s:38:"Resources/Private/Layouts/Default.html";s:4:"54d0";s:46:"Resources/Private/Templates/Backend/Error.html";s:4:"c30f";s:47:"Resources/Private/Templates/Backend/Render.html";s:4:"4877";}',
);

?>
