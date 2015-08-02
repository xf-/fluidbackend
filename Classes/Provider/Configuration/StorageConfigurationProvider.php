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
use FluidTYPO3\Flux\Utility\ExtensionNamingUtility;
use FluidTYPO3\Flux\View\TemplatePaths;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Configuration Provider for EXT:fluidbackend storage records
 *
 * @author Claus Due
 * @package Fluidbackend
 * @subpackage Provider\Configuration
 */
class StorageConfigurationProvider extends AbstractProvider implements ProviderInterface {

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
		} else {
			list ($extensionName, ) = $this->getExtensionKeyAndActionFromUrl();
		}
		return ExtensionNamingUtility::getExtensionKey($extensionName);
	}

	/**
	 * @param array $row
	 * @return array|NULL
	 */
	public function getTemplatePaths(array $row) {
		$extensionKey = $this->getExtensionKey($row);
		$paths = $this->configurationService->getViewConfigurationForExtensionName($extensionKey);
		return $paths;
	}

	/**
	 * @param array $row
	 * @return NULL|string
	 */
	public function getTemplatePathAndFilename(array $row) {
		$action = $this->getControllerActionFromRecord($row);
		$paths = $this->getTemplatePaths($row);
		$templatePaths = new TemplatePaths($paths);
		return $templatePaths->resolveTemplateFileForControllerAndActionAndFormat('Backend', ucfirst($action));
	}

	/**
	 * @param array $row
	 * @return array
	 */
	public function getFlexFormValues(array $row) {
		return array(
			'record' => $row,
			'fluxRecordField' => $this->getFieldName($row),
			'fluxTableName' => $this->getTableName($row)
		);
	}

}
