<?php
namespace FluidTYPO3\Fluidbackend\Service;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Flux\Core;
use FluidTYPO3\Flux\Service\FluxService;
use FluidTYPO3\Flux\Utility\ResolveUtility;
use FluidTYPO3\Flux\Utility\PathUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Configuration Service
 *
 * Provides methods to read various configuration related
 * to Flux Backend Modules.
 *
 * @author Claus Due
 * @package Fluidbackend
 * @subpackage Service
 */
class ConfigurationService extends FluxService implements SingletonInterface {

	/**
	 * @param string $extensionName
	 * @return array
	 */
	public function getBackendModuleTemplatePaths($extensionName = NULL) {
		$typoScript = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$paths = array();
		$extensionKeys = Core::getRegisteredProviderExtensionKeys('Backend');
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
	 * @param array $fluxConfiguration
	 * @return void
	 * @throws \Exception
	 */
	public function registerModuleBasedOnFluxForm($extensionKey, array $fluxConfiguration) {
		$module = 'web';
		if (TRUE === isset($fluxConfiguration['moduleGroup'])) {
			$module = $fluxConfiguration['moduleGroup'];
		}
		$position = 'before:help';
		if (TRUE === isset($fluxConfiguration['modulePosition'])) {
			$position = $fluxConfiguration['modulePosition'];
		}
		$navigationComponent = '';
		if (TRUE === isset($fluxConfiguration['modulePageTree']) && TRUE === (boolean) $fluxConfiguration['modulePageTree']) {
			$navigationComponent = 'typo3-pagetree';
		}
		$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionKey);
		$extensionKey = strtolower($extensionKey);
		$icon = 'EXT:' . $extensionKey . '/ext_icon.gif';
		if ($fluxConfiguration['icon']) {
			$icon = $fluxConfiguration['icon'];
		}
		if (NULL === ResolveUtility::resolveFluxControllerClassNameByExtensionKeyAndAction($extensionKey, 'render', 'Backend')) {
			throw new \Exception('Attempt to register a Backend controller without an associated BackendController. Extension key: ' . $extensionKey, 1368826271);
		}
		$signature = str_replace('_', '', $extensionKey);
		$moduleConfiguration = array(
			'access' => 'user,group',
			'icon'   => $icon,
			'labels' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_module_' . $fluxConfiguration['id'] . '.xml'
		);
		if (FALSE === empty($navigationComponent)) {
			$moduleConfiguration['navigationComponentId'] = $navigationComponent;
		}
		$moduleSignature = 'tx_' . $signature . '_' . $fluxConfiguration['id'];
		if (FALSE === isset($GLOBALS['TBE_MODULES'][$module])) {
			if (FALSE === strpos($position, ':')) {
				if ('top' === $position) {
					$temp_TBE_MODULES = array($module => '');
					$temp_TBE_MODULES = GeneralUtility::array_merge_recursive_overrule($temp_TBE_MODULES, $GLOBALS['TBE_MODULES']);
				} else {
					$temp_TBE_MODULES = $GLOBALS['TBE_MODULES'];
					$temp_TBE_MODULES[$module] = '';
				}
			} else {
				list ($command, $relativeKey) = explode(':', $position);
				foreach ($GLOBALS['TBE_MODULES'] as $key => $val) {
					if ($key === $relativeKey) {
						if ('before' === $command) {
							$temp_TBE_MODULES[$module] = '';
							$temp_TBE_MODULES[$key] = $val;
						} else {
							$temp_TBE_MODULES[$key] = $val;
							$temp_TBE_MODULES[$module] = '';
						}
					} else {
						$temp_TBE_MODULES[$key] = $val;
					}
				}
			}
			$GLOBALS['TBE_MODULES'] = $temp_TBE_MODULES;
			$moduleConfiguration['labels'] = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_module_' . $module . '.xml';
            ExtensionUtility::registerModule($extensionKey, $module, '', $position, array('Backend' => 'render,save'), $moduleConfiguration);
		}
        ExtensionUtility::registerModule(
			$extensionKey,
			$module,
			$moduleSignature,
			$position,
			array(
				'Backend' => 'render,save',
			),
			$moduleConfiguration
		);
	}

	/**
	 * @return void
	 */
	public function detectAndRegisterAllFluidBackendModules() {
		$configurations = $this->getBackendModuleTemplatePaths();
		foreach ($configurations as $extensionKey => $paths) {
			$extensionName = GeneralUtility::underscoredToUpperCamelCase($extensionKey);
			$paths = PathUtility::translatePath($paths);
			$directoryPath = $paths['templateRootPath'] . '/Backend/';
			$files = GeneralUtility::getFilesInDir($directoryPath, 'html');
			foreach ($files as $fileName) {
				$templatePathAndFilename = $directoryPath . $fileName;
				$storedConfiguration = $this->getFlexFormConfigurationFromFile($templatePathAndFilename, array(), 'Configuration', $paths, $extensionName);
				if (FALSE === (boolean) $storedConfiguration['enabled']) {
					continue;
				}
				$this->registerModuleBasedOnFluxForm($extensionKey, $storedConfiguration);
			}
		}
	}

}
