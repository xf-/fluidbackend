<?php
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

/**
 * ## Outlet Definition: Controller Action
 *
 * Saves data from the form in a special record type
 * added by this extension - this record can then be
 * read from other scripts/templates and values retrieved.
 *
 * @package Fluidbackend
 * @subpackage Outlet
 */
class Tx_Fluidbackend_Outlet_ControllerOutlet extends Tx_Fluidbackend_Outlet_AbstractOutlet implements Tx_Fluidbackend_Outlet_OutletInterface {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var string
	 */
	protected $controller;

	/**
	 * @var string
	 */
	protected $action;

	/**
	 * @var string
	 */
	protected $extensionName;

	/**
	 * @var array
	 */
	protected $arguments = array();

	/**
	 * @var string
	 */
	protected $outlet = 'Controller';

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param string $controller
	 * @param void
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * @param string $action
	 * @return void
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @param string $extensionName
	 * @return void
	 */
	public function setExtensionName($extensionName) {
		$this->extensionName = $extensionName;
	}

	/**
	 * @return string
	 */
	public function getExtensionName() {
		return $this->extensionName;
	}

	/**
	 * @param array $arguments
	 * @return void
	 */
	public function setArguments(array $arguments) {
		$this->arguments = $arguments;
	}

	/**
	 * @return array
	 */
	public function getArguments() {
		return $this->arguments;
	}

	/**
	 * @return string
	 */
	public function getTarget() {
		return $this->getExtensionName() . '::' . $this->getController() . '->' . $this->getAction();
	}

	/**
	 * @param array $data
	 * @return void
	 */
	public function produce(array $data) {
		$arguments = (array) $this->getArguments();
		/** @var $request Tx_Extbase_MVC_Web_Request */
		$request = $this->objectManager->get('Tx_Extbase_MVC_Web_Request');
		$request->setControllerName($this->getController());
		$request->setControllerActionName($this->getAction());
		$request->setControllerExtensionName($this->getExtensionName());
		$request->setArguments($arguments);
		$request->setArgument('settings', $data);
		/** @var $response Tx_Extbase_MVC_Web_Response */
		$response = $this->objectManager->get('Tx_Extbase_MVC_Web_Response');
		/** @var $dispatcher Tx_Extbase_MVC_Dispatcher */
		$dispatcher = $this->objectManager->get('Tx_Extbase_MVC_Dispatcher');
		$dispatcher->dispatch($request, $response);
		$output = $response->getContent();
		$this->message($output);
	}

}
