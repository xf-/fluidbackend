<?php
namespace FluidTYPO3\Fluidbackend\ViewHelpers\Outlet;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@wildside.dk>, Wildside A/S
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
use FluidTYPO3\Fluidbackend\Outlet\OutletInterface;
use FluidTYPO3\Fluidbackend\Outlet\ControllerOutlet;

/**
 * ### Controller Outlet Definition ViewHelper
 *
 * Defines one data Outlet which uses an Extbase controller to
 * process the posted data. The controller action and extension
 * name affinity can be set, but the controller name itself is
 * always "Backend".
 *
 * #### Example
 *
 *     <be:outlet.controller action="doStuff" extensionName="MyExtension" arguments="{0: 'foo', 1: 'bar'}" />
 *
 * Would call:
 *
 *     $arg1 = $arguments[0]; // string "foo"
 *     $arg2 = $arguments[1]; // string "bar"
 *     Tx_MyExtension_Controller_BackendController->doStuff($settings, $arg1, $arg2);
 *
 * Where naturally, $settings is the array result of posting
 * the form data.
 *
 * #### Advanced example
 *
 * If you change the type of the first argument on the action
 * called on your controller, the data will be first transformed
 * to match the desired type - using Extbase argument
 * transformations. Which means that if you want to use for
 * example a model object to store the posted data, simply tell
 * Extbase that the first argument is not an array but an instance
 * of the desired model class. Extbase will then construct a
 * (not yet persisted) version of that model object.
 *
 * #### Multiple controllers
 *
 * It is possible to have multiple controller Outlets with any
 * number of different argument types - for example controller
 * Outlet A might save one Domain record type while Outlet B
 * might want another type - or simply use a standard array.
 *
 * @package Fluidbackend
 * @subpackage ViewHelpers\Outlet
 */
class ControllerViewHelper extends AbstractOutletViewHelper {

	/**
	 * @var string
	 */
	protected $outletClassName = 'Controller';

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('action', 'string', 'Name of controller action, excluding the "Action" part - for example "save" (which is the default action to use)', FALSE, 'save');
		$this->registerArgument('controller', 'string', 'Name of the controller which should be called, defaults to "Backend"', FALSE, 'Backend');
		$this->registerArgument('extensionName', 'string', 'Name of the extension, in UpperCamelCase, which contains the controller class. If not specified this is read from the rendering context (which uses your extension scope)', FALSE, NULL);
		$this->registerArgument('arguments', 'array', 'Optional array of arguments passed, in addition to the non-optional first argument (array $settings) - i.e. these arguments are used as no. 2, 3, 4 etc.', FALSE, array());
	}

	/**
	 * @return OutletInterface
	 */
	protected function createOutletFromArguments() {
		if (TRUE === empty($this->arguments['extensionName'])) {
			$extensionName = $this->renderingContext->getControllerContext()->getRequest()->getControllerExtensionName();
		} else {
			$extensionName = $this->arguments['extensionName'];
		}
		if (FALSE === is_array($this->arguments['arguments'])) {
			// catches cases where NULL gets passed which for example might happen if these come from a TS setting used in the Fluid template
			// which is perfectly fine - but we need to be able to rely on this value ALWAYS being an array.
			$arguments = array();
		} else {
			$arguments = $this->arguments['arguments'];
		}
		/** @var $outlet ControllerOutlet */
		$outlet = parent::createOutletFromArguments();
		$outlet->setController($this->arguments['controller']);
		$outlet->setAction($this->arguments['action']);
		$outlet->setExtensionName($extensionName);
		$outlet->setArguments($arguments);
		return $outlet;
	}

}
