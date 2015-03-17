<?php
namespace FluidTYPO3\Fluidbackend\Controller;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Fluidbackend\Domain\Repository\ConfigurationRepository;
use FluidTYPO3\Fluidbackend\Domain\Model\Configuration;
use FluidTYPO3\Flux\Controller\AbstractFluxController;
use FluidTYPO3\Flux\Outlet\OutletInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Abstract Backend Controller
 *
 * @package Fluidbackend
 * @subpackage Controller
 */
class AbstractBackendController extends AbstractFluxController {

	/**
	 * @var string
	 */
	const SAVE_ACTION = 'save';

	/**
	 * @var ConfigurationRepository
	 */
	protected $configurationRepository;

	/**
	 * @var Configuration
	 */
	protected $configuration;

	/**
	 * @var \TYPO3\CMS\Backend\Form\FormEngine
	 */
	protected $formHandler;

	/**
	 * @var string
	 */
	protected $fluxTableName = 'tx_fluidbackend_domain_model_configuration';

	/**
	 * @var string
	 */
	protected $fluxRecordField = 'configuration';

	/**
	 * @param ConfigurationRepository $configurationRepository
	 * @return void
	 */
	public function injectConfigurationRepository(ConfigurationRepository $configurationRepository) {
		$this->configurationRepository = $configurationRepository;
	}

	/**
	 * @param ViewInterface $view
	 * @return void
	 */
	public function initializeView(ViewInterface $view) {
		parent::initializeView($view);
		$name = $this->getCurrentConfigurationName();
		$pageUid = $this->getCurrentPageUid();
		$this->configuration = $this->configurationRepository->findOrCreateOneByNameInPid($name, $pageUid);
		$this->formHandler = $this->objectManager->get('TYPO3\CMS\Backend\Form\FormEngine');
		$this->formHandler->initDefaultBEmode();
		$this->formHandler->enableClickMenu = TRUE;
		$this->formHandler->enableTabMenu = TRUE;
		$this->view->assign('formHandler', $this->formHandler);
		$this->view->assign('configurationName', $name);
		$this->view->setControllerContext($this->controllerContext);
	}

	/**
	 * @return OutletInterface
	 */
	protected function getCurrentOutlet() {
		return $this->provider->getForm($this->getRecord())->getOutlet();
	}

	/**
	 * @return integer
	 */
	protected function getCurrentPageUid() {
		$pageUid = GeneralUtility::_GET('id');
		return intval($pageUid);
	}

	/**
	 * @param array $data
	 * @return string
	 */
	protected function getIdFromUrlParameterOrData(array $data) {
		$module = GeneralUtility::_GET('M');
		$module = GeneralUtility::camelCaseToLowerCaseUnderscored((string) $module);
		$id = (FALSE === empty($module) ? array_pop(explode('_', $module)) : $data['id']);
		return $id;
	}

	/**
	 * @return string
	 */
	protected function getCurrentConfigurationName() {
		if (self::SAVE_ACTION === $this->request->getControllerActionName()) {
			return $this->request->getArgument('configurationName');
		}
		$data = $this->getData();
		$module = $this->getIdFromUrlParameterOrData($data);
		$extensionName = $this->request->getControllerExtensionName();
		$actionName = $this->request->getControllerActionName();
		$name = implode('-', array($extensionName, $actionName, $id));
		return $name;
	}

	/**
	 * @return array|FALSE
	 */
	public function getRecord() {
		$name = $this->getCurrentConfigurationName();
		$pageUid = $this->getCurrentPageUid();
		$results = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->fluxTableName, "name = '" . $name . "' AND pid = '" . $pageUid . "' AND deleted = 0 AND hidden = 0", 1);
		$record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($results);
		if (TRUE === empty($record)) {
			$record = array(
				'tstamp' => time(),
				'crdate' => time(),
				'name' => $name,
				'lagel' => 'AUTO: ' . $name,
				'pid' => $pageUid,
			);
			$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->fluxTableName, $record);
		}
		return $record;
	}

	/**
	 * Stub render method
	 *
	 * Override this in your custom controller to gain better control
	 * over assigned template variables etc.
	 *
	 * @return void
	 */
	public function renderAction() {
	}


	/**
	 * Stub beforeSave method
	 *
	 * Override in your custom controller to pre-process the data.
	 * Must return the same array with any processing applied.
	 *
	 * @param array $settings
	 * @return array
	 */
	protected function beforeSave(array $settings) {
		return $settings;
	}

	/**
	 * Stub afterSave method
	 *
	 * Override in your custom controller to post-process the data.
	 * Must return the same array with any processing applied.
	 *
	 * Note: happens BEFORE the data is saved to DB but AFTER every
	 * Outlet has produced output. This allows you to one last time
	 * adjust the data before it gets saved - regardless of how or if
	 * Outlets managed to process the data.
	 *
	 * @param array $settings
	 * @return array
	 */
	protected function afterSave(array $settings) {
		return $settings;
	}

	/**
	 * @param OutletInterface $outlet
	 * @param array $settings
	 * @return array
	 */
	protected function beforeOutlet(OutletInterface $outlet, array $settings) {
		return $settings;
	}

	/**
	 * Stub method: after Outlet produces content.
	 *
	 * Note: does not need to return any value; when the data passed
	 * to the Outlet is done being output, it is discarded after it
	 * gets passed to this method (which means that if you need to
	 * save a post-result, use this method to ensure the Outlet has
	 * not produced any errors).
	 *
	 * @param OutletInterface $outlet
	 * @param array $settings
	 * @return void
	 */
	protected function afterOutlet(OutletInterface $outlet, array $settings) {
	}

	/**
	 * Do what needs to be done when Outlet produces an error. If you
	 * override this method and it does NOT re-throw the Exception,
	 * processing will continue (skipping the current Outlet).
	 *
	 * @param OutletInterface $outlet
	 * @param array $settings
	 * @param \Exception $error
	 * @return void
	 * @throws \Exception
	 */
	protected function onOutletError(OutletInterface $outlet, array $settings, \Exception $error) {
		throw $error;
	}

	/**
	 * Final method: save form
	 *
	 * While overriding this method is NOT allowed, you will be able
	 * to use the method beforeSave($settings) to transform the pre-
	 * processed array of form data (language subnode nesting removed)
	 * to perform your own tasks.
	 *
	 * Note: there is an afterSave($settings) for the same reason.
	 *
	 * @param array $settings Array of (raw) data posted from TCEforms, contains language subnode wrappers
	 * @param string $configurationName Unused argument: is only used for detection in getCurrentConfigurationName
	 * @return void
	 */
	final public function saveAction(array $settings, $configurationName) {
		$settings = $this->trimLanguageWrappersFromPostedData($settings);
		$outlet = $this->getCurrentOutlet();
		$settings = $this->beforeSave($settings);
		$table = $this->getFluxTableName();
		$field = $this->getFluxRecordField();
		$uid = key($settings[$table]);
		$settings = $settings[$table][$uid][$field]['data'];
		try {
			$localSettings = $settings;
			$localSettings = $this->deepenDottedArray($localSettings);
			$localSettings = $this->beforeOutlet($outlet, $localSettings);
			$outlet->fill($localSettings);
			$outlet->produce();
			$this->afterOutlet($outlet, $localSettings);
		} catch (\Exception $outletError) {
			$this->onOutletError($outlet, $localSettings, $outletError);
		}
		$settings = $this->afterSave($settings);
		$this->updateConfigurationStorage($settings);
		$this->redirect('render');
	}

	/**
	 * @param array $settings
	 * @return void
	 */
	protected function updateConfigurationStorage(array $settings) {
		$dom = new \DOMDocument('1.0', 'utf-8');
		$root = $dom->appendChild($dom->createElement('T3FlexForms'));
		$data = $root->appendChild($dom->createElement('data'));
		foreach ($settings as $sheetName => $fieldValues) {
			$sheet = $data->appendChild($dom->createElement('sheet'));
			$language = $sheet->appendChild($dom->createElement('language'));
			$language = $this->addIndexAttribute($dom, $language, 'lDEF');
			$this->addIndexAttribute($dom, $sheet, $sheetName)->appendChild($language);
			foreach ($fieldValues as $fieldName => $fieldValue) {
				$field = $language->appendChild($dom->createElement('field'));
				$value = $field->appendChild($dom->createElement('value'));
				$this->addIndexAttribute($dom, $value, 'vDEF')->appendChild($dom->createTextNode($fieldValue));
				$this->addIndexAttribute($dom, $field, $fieldName)->appendChild($value);
			}
		}
		$dom->preserveWhiteSpace = FALSE;
		$dom->formatOutput = TRUE;
		$xml = $dom->saveXML();
		$configurationName = $this->getCurrentConfigurationName();
		$storagePid = $this->getCurrentPageUid();
		$configuration = $this->configurationRepository->findOrCreateOneByNameInPid($configurationName, $storagePid);
		$configuration->setConfiguration($xml);
		$this->configurationRepository->update($configuration);
	}

	/**
	 * @param \DOMDocument $dom
	 * @param \DOMNode $element
	 * @param string $indexValue
	 * @return \DOMNode
	 */
	protected function addIndexAttribute(\DOMDocument $dom, \DOMNode $element, $indexValue) {
		$index = $dom->createAttribute('index');
		$index->value = $indexValue;
		$element->appendChild($index);
		return $element;
	}

	/**
	 * @param array $array
	 * @return array
	 */
	protected function deepenDottedArray(array $array) {
		foreach ($array as $valueKey => $nodeValue) {
			if (TRUE === is_array($nodeValue)) {
				$array[$valueKey] = $this->deepenDottedArray($nodeValue);
			} elseif (FALSE !== strpos($valueKey, '.')) {
				$valueKeyParts = explode('.', $valueKey);
				$currentNode = &$array;
				foreach ($valueKeyParts as $valueKeyPart) {
					$currentNode = &$currentNode[$valueKeyPart];
				}
				$currentNode = $nodeValue;
				unset($array[$valueKey]);
			}
		}
		return $array;
	}

	/**
	 * @param mixed $post
	 * @param integer $level
	 * @return mixed
	 */
	public function trimLanguageWrappersFromPostedData($post, $level = 0) {
		foreach ($post as $name => $value) {
			if ($name === 'vDEF' || $name === 'lDEF') {
				return $this->trimLanguageWrappersFromPostedData($value, $level + 1);
			} elseif (TRUE === is_array($value)) {
				$post[$name] = $this->trimLanguageWrappersFromPostedData($value, $level + 1);
			}
		}
		return $post;
	}

}
