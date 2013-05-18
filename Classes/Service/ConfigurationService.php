<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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

/**
 * Configuration Service
 *
 * Provides methods to read various configuration related
 * to Flux Backend Modules.
 *
 * @author Claus Due, Wildside A/S
 * @package Fluidbackend
 * @subpackage Service
 */
class Tx_Fluidbackend_Service_ConfigurationService extends Tx_Flux_Service_FluxService implements t3lib_Singleton {

	/**
	 * @param string $extensionName
	 * @return array
	 */
	public function getBackendModuleTemplatePaths($extensionName = NULL) {
		$typoScript = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		if (TRUE === isset($typoScript['plugin.']['tx_fluidbackend.']['view.'])) {
			$ownPaths = $typoScript['plugin.']['tx_fluidbackend.']['view.'];
		} else {
			$ownPaths = array(
				'templateRootPath' => 'EXT:fluidbackend/Resources/Private/Templates/',
				'layoutRootPath' => 'EXT:fluidbackend/Resources/Private/Layouts/',
				'partialRootPath' => 'EXT:fluidbackend/Resources/Private/Partials/',
			);
		}
		$paths = array(
			'fluidbackend' => $ownPaths
		);
		$extensionKeys = Tx_Flux_Core::getRegisteredProviderExtensionKeys('Backend');
		foreach ($extensionKeys as $extensionKey) {
			$pluginSignature = str_replace('_', '', $extensionKey);
			if (TRUE === isset($typoScript['plugin.']['tx_' . $pluginSignature . '.']['view.'])) {

				$paths[$extensionKey] = $typoScript['plugin.']['tx_' . $pluginSignature . '.']['view.'];
			}
		}
		return $paths;
	}

	/**
	 * @param string $extensionKey
	 * @param string $module
	 * @param array $fluxConfiguration
	 * @return void
	 * @throws Exception
	 */
	public function registerModuleBasedOnFluxForm($extensionKey, $module, array $fluxConfiguration) {
		$extensionKey = t3lib_div::camelCaseToLowerCaseUnderscored($extensionKey);
		$extensionKey = strtolower($extensionKey);
		$icon = 'EXT:' . $extensionKey . '/ext_icon.gif';
		if ($fluxConfiguration['icon']) {
			$icon = $fluxConfiguration['icon'];
		}
		if (FALSE === $this->detectControllerClassPresenceFromExtensionKeyAndControllerType($extensionKey, 'Backend')) {
			throw new Exception('Attempt to register a Backend controller without an associated BackendController. Extension key: ' . $extensionKey, 1368826271);
		}
		$signature = str_replace('_', '', $extensionKey);
		Tx_Extbase_Utility_Extension::registerModule(
			$extensionKey,
			$module,
			'tx_' . $signature . '_' . $fluxConfiguration['id'],
			'Backend module',
			array(
				'Backend' => 'render,save',
			),
			array(
				'access' => 'user,group',
				'icon'   => $icon,
				'labels' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_module_' . $fluxConfiguration['id'] . '.xml',
			)
		);
	}

	/**
	 * @return void
	 */
	public function detectAndRegisterAllFluidBackendModules() {
		$configurations = $this->getBackendModuleTemplatePaths();
		foreach ($configurations as $extensionKey => $paths) {
			$extensionName = t3lib_div::underscoredToUpperCamelCase($extensionKey);
			$paths = Tx_Flux_Utility_Path::translatePath($paths);
			$directoryPath = $paths['templateRootPath'] . '/Backend/';
			$files = t3lib_div::getFilesInDir($directoryPath, 'html');
			foreach ($files as $fileName) {
				$templatePathAndFilename = $directoryPath . $fileName;
				$storedConfiguration = $this->getFlexFormConfigurationFromFile($templatePathAndFilename, array(), 'Configuration', $paths, $extensionName);
				if (FALSE === (boolean) $storedConfiguration['enabled']) {
					continue;
				}
				$this->registerModuleBasedOnFluxForm($extensionKey, 'web', $storedConfiguration);
			}
		}
	}

}