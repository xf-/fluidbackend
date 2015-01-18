<?php
namespace FluidTYPO3\Fluidbackend\Outlet;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

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
class ControllerOutlet extends AbstractOutlet implements OutletInterface {

	/**
	 * @var ObjectManagerInterface
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
	 * @param ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
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
		/** @var $request \TYPO3\CMS\Extbase\Mvc\Web\Request */
		$request = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Web\Request');
		$request->setControllerName($this->getController());
		$request->setControllerActionName($this->getAction());
		$request->setControllerExtensionName($this->getExtensionName());
		$request->setArguments($arguments);
		$request->setArgument('settings', $data);
		/** @var $response \TYPO3\CMS\Extbase\Mvc\Web\Response */
		$response = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Web\Response');
		/** @var $dispatcher \TYPO3\CMS\Extbase\Mvc\Dispatcher */
		$dispatcher = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Dispatcher');
		$dispatcher->dispatch($request, $response);
		$output = $response->getContent();
		$this->message($output);
	}

}
