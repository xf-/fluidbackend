<?php
namespace FluidTYPO3\Fluidbackend\Service;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Fluidbackend\Constants;
use FluidTYPO3\Flux\Core;
use FluidTYPO3\Flux\Form;
use FluidTYPO3\Flux\Service\FluxService;
use FluidTYPO3\Flux\Utility\ExtensionNamingUtility;
use FluidTYPO3\Flux\Utility\MiscellaneousUtility;
use FluidTYPO3\Flux\Utility\ResolveUtility;
use FluidTYPO3\Flux\Utility\PathUtility;
use FluidTYPO3\Flux\View\TemplatePaths;
use FluidTYPO3\Flux\View\ViewContext;
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
	 * @return array
	 */
	protected function getRegisteredProviderExtensionKeys() {
		return Core::getRegisteredProviderExtensionKeys('Backend');
	}

	/**
	 * @param string $extensionName
	 * @return array
	 */
	public function getBackendModuleTemplatePaths($extensionName = NULL) {
		$extensionKeys = $this->getRegisteredProviderExtensionKeys();
		return array_map(array($this, 'getViewConfigurationForExtensionName'), array_combine($extensionKeys, $extensionKeys));
	}

	/**
	 * @param string $qualifiedExtensionName
	 * @param Form $form
	 * @return void
	 * @throws \RuntimeException
	 */
	public function registerModuleBasedOnFluxForm($qualifiedExtensionName, Form $form) {
		$extensionKey = ExtensionNamingUtility::getExtensionKey($qualifiedExtensionName);
		$signature = ExtensionNamingUtility::getExtensionSignature($qualifiedExtensionName);
		$options = $form->getOption('Fluidbackend');
		$formId = $form->getName();
		$module = 'web';
		if (TRUE === isset($options[Constants::FORM_OPTION_MODULE_GROUP])) {
			$module = $options[Constants::FORM_OPTION_MODULE_GROUP];
		}
		$position = 'before:help';
		if (TRUE === isset($options[Constants::FORM_OPTION_MODULE_POSITION])) {
			$position = $options[Constants::FORM_OPTION_MODULE_POSITION];
		}
		$navigationComponent = '';
		if (TRUE === isset($options[Constants::FORM_OPTION_MODULE_PAGE_TREE])
			&& TRUE === (boolean) $options[Constants::FORM_OPTION_MODULE_PAGE_TREE]) {
			$navigationComponent = 'typo3-pagetree';
		}
		$icon = MiscellaneousUtility::getIconForTemplate($form);
		if (TRUE === empty($icon)) {
			$icon = 'EXT:' . $extensionKey . '/ext_icon.gif';
		}
		if (NULL === $this->getResolver()->resolveFluxControllerClassNameByExtensionKeyAndAction($qualifiedExtensionName, 'render', 'Backend')) {
			throw new \RuntimeException(
				'Attempt to register a Backend controller without an associated BackendController. Extension key: ' . $extensionKey,
				1368826271);
		}
		$moduleConfiguration = array(
			'access' => 'user,group',
			'icon'   => $icon,
			'labels' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_module_' . $formId . '.xml'
		);
		if (FALSE === empty($navigationComponent)) {
			$moduleConfiguration['navigationComponentId'] = $navigationComponent;
		}
		$moduleSignature = 'tx_' . $signature . '_' . ucfirst($formId);
		if (FALSE === isset($GLOBALS['TBE_MODULES'][$module])) {
			if (FALSE === strpos($position, ':')) {
				if ('top' === $position) {
					$temp_TBE_MODULES = array($module => '');
					$temp_TBE_MODULES = GeneralUtility::array_merge_recursive_overrule($temp_TBE_MODULES, $GLOBALS['TBE_MODULES']);
				} else {
					$temp_TBE_MODULES = (array) $GLOBALS['TBE_MODULES'];
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
			$GLOBALS['TBE_MODULES'] = (array) $temp_TBE_MODULES;
			// register pseudo-module acting as group header
			$moduleConfiguration['labels'] = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_modulegroup.xml';
            ExtensionUtility::registerModule(
				$qualifiedExtensionName,
				$module,
				'',
				$position,
				array('Backend' => 'render,save'),
				$moduleConfiguration
			);
		}
		// register individual module in group
		$moduleConfiguration['labels'] = 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_module_' . $formId . '.xml';
        ExtensionUtility::registerModule(
			$qualifiedExtensionName,
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
			$paths = new TemplatePaths($paths);
			$context = new ViewContext(NULL, $extensionKey, 'Backend');
			$context->setSectionName('Configuration');
			$context->setTemplatePaths($paths);
			$files = $paths->resolveAvailableTemplateFiles('Backend');
			foreach ($files as $fileName) {
				$templatePathAndFilename = $directoryPath . $fileName;
				$context->setTemplatePathAndFilename($templatePathAndFilename);
				$form = $this->getFormFromTemplateFile($context);
				$this->registerModuleBasedOnFluxForm($extensionKey, $form);
			}
		}
	}

}
