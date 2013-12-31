<?php
namespace FluidTYPO3\Fluidbackend\Controller;
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
 *  the Free Software Foundation; either version 3 of the License, or
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
use FluidTYPO3\Fluidbackend\Domain\Repository\ConfigurationRepository;
use FluidTYPO3\Fluidbackend\Domain\Model\Configuration;
use FluidTYPO3\Fluidbackend\Outlet\OutletInterface;
use FluidTYPO3\Fluidbackend\Service\OutletService;
use FluidTYPO3\Flux\Controller\AbstractFluxController;

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
	 * @var OutletService
	 */
	protected $outletService;

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
	 * @param OutletService $outletService
	 * @return void
	 */
	public function injectOutletService(OutletService $outletService) {
		$this->outletService = $outletService;
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
	}

	/**
	 * @return array
	 */
	protected function getCurrentOutlets() {
		$moduleData = $this->getData();
		if (TRUE === isset($moduleData['outlets'])) {
			$outlets = $moduleData['outlets'];
		} else {
			/** @var $outlet \FluidTYPO3\Fluidbackend\Outlet\FlashMessageOutlet */
			$outlet = $this->objectManager->get('FluidTYPO3\Fluidbackend\Outlet\FlashMessageOutlet');
			$outlet->setName('default');
			$outlet->setLabel('No Outlets defined');
			$outlets = array($outlet);

		}
		return $outlets;
	}

	/**
	 * @return integer
	 */
	protected function getCurrentPageUid() {
		$pageUid = GeneralUtility::_GET('id');
		return intval($pageUid);
	}

	/**
	 * @return string
	 */
	protected function getCurrentConfigurationName() {
		if (self::SAVE_ACTION === $this->request->getControllerActionName()) {
			return $this->request->getArgument('configurationName');
		}
		$data = $this->getData();
		$module = GeneralUtility::_GET('M');
		if (FALSE === empty($module)) {
			$id = array_pop(explode('_', GeneralUtility::camelCaseToLowerCaseUnderscored($module)));
		} else {
			$id = $data['id'];
		}
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
		$outlets = $this->getCurrentOutlets();
		$settings = $this->outletService->trimLanguageWrappersFromPostedData($settings[$this->fluxTableName]);
		$settings = $this->beforeSave($settings);
		$uid = key($settings);
		$settings = $settings[$uid][$this->fluxRecordField]['data'];
		foreach ($outlets as $outlet) {
			try {
				$localSettings = $settings;
				if (TRUE === $outlet->assertDeepenSettings()) {
					$localSettings = $this->deepenDottedArray($localSettings);
				}
				$localSettings = $this->beforeOutlet($outlet, $localSettings);
				$this->outletService->produce($outlet, $localSettings);
				$this->afterOutlet($outlet, $localSettings);
			} catch (\Exception $outletError) {
				$this->onOutletError($outlet, $localSettings, $outletError);
			}
		}
		$settings = $this->afterSave($settings);
		$this->updateConfigurationStorage($settings);
		$this->redirect('render');
	}

	/**
	 * Stub error action
	 *
	 * Override this in your controller to render a pretty page if
	 * fatal errors occur when rendering any part of your template
	 * or accepting form data.
	 *
	 * @return void
	 */
	public function errorAction() {
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

}
