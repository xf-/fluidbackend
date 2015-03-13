<?php
namespace FluidTYPO3\Fluidbackend\Override\Backend\Module;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Override: sysext "backend" ModuleLoader
 *
 * Extended with mechanisms for loading Fluid backend modules.
 *
 * @author Claus Due
 * @package Fluidbackend
 * @subpackage Override\Backend\Controller
 */
class ModuleLoader extends \TYPO3\CMS\Backend\Module\ModuleLoader {

	/**
	 * @param array $modulesArray
	 * @param string $BE_USER
	 */
	public function load($modulesArray, $BE_USER = NULL) {
		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		/** @var $configurationService \FluidTYPO3\Fluidbackend\Service\ConfigurationService */
		$configurationService = $objectManager->get('FluidTYPO3\Fluidbackend\Service\ConfigurationService');
		$configurationService->detectAndRegisterAllFluidBackendModules();
		$modulesArray = (array) $GLOBALS['TBE_MODULES'];
		$this->performModuleLoading($modulesArray, $BE_USER);
	}

	/**
	 * @codeCoverageIgnore
	 * @param array $modules
	 * @param mixed $user
	 */
	protected function performModuleLoading(array $modules, $user) {
		parent::load($modules, NULL === $BE_USER ? $GLOBALS['BE_USER'] : $BE_USER);
	}

}
