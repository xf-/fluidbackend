<?php
namespace FluidTYPO3\Fluidbackend\Override\Backend\Module;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
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
	public function load($modulesArray, $BE_USER = '') {
		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		/** @var $configurationService \FluidTYPO3\Fluidbackend\Service\ConfigurationService */
		$configurationService = $objectManager->get('FluidTYPO3\Fluidbackend\Service\ConfigurationService');
		$configurationService->detectAndRegisterAllFluidBackendModules();
		$modulesArray = $GLOBALS['TBE_MODULES'];
		parent::load($modulesArray, $BE_USER);
	}

}
