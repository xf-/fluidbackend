<?php
namespace FluidTYPO3\Fluidbackend\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper as FluidFormViewHelper;
use FluidTYPO3\Flux\Service\FluxService;

/**
 * ## Main form rendering ViewHelper
 *
 * Used in Fluidbackend page templates to render the form,
 * inserted at the location of this tag.
 *
 * @package Fluidbackend
 * @subpackage ViewHelpers
 */
class FormViewHelper extends FluidFormViewHelper {

	/**
	 * @var FluxService
	 */
	protected $configurationService;

	/**
	 * @param FluxService $configurationService
	 * @return void
	 */
	public function injectConfigurationService(FluxService $configurationService) {
		$this->configurationService = $configurationService;
	}

	/**
	 * @param string $pluginName
	 * @return string
	 */
	public function render($pluginName = NULL) {
		$data = $this->templateVariableContainer->getAll();
		$row = $data['record'];
		$field = $data['fluxRecordField'];
		$table = $data['fluxTableName'];
		/** @var $formHandler \TYPO3\CMS\Backend\Form\FormEngine */
		$formHandler = $data['formHandler'];
		$formHandler->prependFormFieldNames = $this->getFieldNamePrefix() . '[settings]';
		$content = '';
		$content .= $formHandler->printNeededJSFunctions_top();
		$content .= $formHandler->getSoloField($table, $row, $field);
		$content .= $formHandler->printNeededJSFunctions();
		return $content;
	}

}
