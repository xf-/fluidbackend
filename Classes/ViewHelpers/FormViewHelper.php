<?php
namespace FluidTYPO3\Fluidbackend\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@namelesscoder.net>
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
 *****************************************************************/
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
	 * @return string
	 */
	public function render() {
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