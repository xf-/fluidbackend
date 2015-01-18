<?php
namespace FluidTYPO3\Fluidbackend\Provider\Configuration;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Flux\Provider\ProviderInterface;
use FluidTYPO3\Flux\Provider\AbstractProvider;
use FluidTYPO3\Flux\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Configuration Provider for EXT:fluidbackend storage records
 *
 * @author Claus Due
 * @package Fluidbackend
 * @subpackage Provider\Configuration
 */
class StorageConfigurationProvider
	extends AbstractProvider
	implements ProviderInterface {

	/**
	 * @var string
	 */
	protected $tableName = 'tx_fluidbackend_domain_model_configuration';

	/**
	 * @var string
	 */
	protected $fieldName = 'configuration';

	/**
	 * @var string
	 */
	protected $extensionKeyAndAction = NULL;

	/**
	 * @return array
	 */
	public function getExtensionKeyAndActionFromUrl() {
		if (NULL !== $this->extensionKeyAndAction) {
			return $this->extensionKeyAndAction;
		}
		list ($module, $identifier) = explode('_', GeneralUtility::_GET('M'));
		list ($signature) = explode('_', $identifier);
		$signature = GeneralUtility::camelCaseToLowerCaseUnderscored($signature);
		list ($parent, $extensionKeyAndAction) = explode('_tx_', $signature);
		unset($module, $parent);
		$this->extensionKeyAndAction = explode('_', $extensionKeyAndAction);
		return $this->extensionKeyAndAction;
	}

	/**
	 * @param array $row
	 * @return string
	 */
	public function getControllerActionFromRecord(array $row) {
		list (, $action) = $this->getExtensionKeyAndActionFromUrl();
		if (TRUE === empty($action) && TRUE === isset($row['name'])) {
			list (, , $action) = explode('-', $row['name']);
		}
		return $action;
	}

	/**
	 * @param array $row
	 * @return NULL|string
	 */
	public function getExtensionKey(array $row) {
		if (TRUE === isset($row['name'])) {
			list ($extensionName, , ) = explode('-', $row['name']);
			$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
		} else {
			list ($extensionKey, ) = $this->getExtensionKeyAndActionFromUrl();
		}
		return $extensionKey;
	}

	/**
	 * @param array $row
	 * @return array|NULL
	 */
	public function getTemplatePaths(array $row) {
		$extensionKey = $this->getExtensionKey($row);
		$paths = $this->configurationService->getTypoScriptSubConfiguration(NULL, 'view', $extensionKey);
		$paths = PathUtility::translatePath($paths);
		return $paths;
	}

	/**
	 * @param array $row
	 * @return NULL|string
	 */
	public function getTemplatePathAndFilename(array $row) {
		$action = $this->getControllerActionFromRecord($row);
		$paths = $this->getTemplatePaths($row);
		$templatePathAndFilename = $paths['templateRootPath'] . '/Backend/' . ucfirst($action) . '.html';
		return $templatePathAndFilename;
	}

	/**
	 * @param array $row
	 * @return array
	 */
	public function getFlexFormValues(array $row) {
		$extensionKey = $this->getExtensionKey($row);
		$extensionName = GeneralUtility::underscoredToUpperCamelCase($extensionKey);
		$paths = $this->getTemplatePaths($row);
		$values = array();
		$section = $this->getConfigurationSectionName($row);
		$templatePathAndFilename = $this->getTemplatePathAndFilename($row);
		$configuration = $this->configurationService->getFlexFormConfigurationFromFile($templatePathAndFilename, $values, $section, $paths, $extensionName);
		return $configuration;
	}

}
