<?php
namespace FluidTYPO3\Fluidbackend\ViewHelpers\Outlet;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Flux\ViewHelpers\AbstractFormViewHelper;
use FluidTYPO3\Fluidbackend\Outlet\OutletInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * ## Outlet Definition ViewHelper
 *
 * Defines one data outlet for a Fluid backend form.
 * Each outlet is updated with the information when
 * the form is saved.
 *
 * @package Fluidbackend
 * @subpackage ViewHelpers\Outlet
 */
abstract class AbstractOutletViewHelper extends AbstractFormViewHelper {

	/**
	 * @var ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @param ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @var string
	 */
	protected $outletClassName = 'FlashMessage';

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('name', 'string', 'Name of the outlet - use only lowerCamelCase values', TRUE);
		$this->registerArgument('label', 'string', 'Label of the outlet - any human-readable value allowed', TRUE);
		$this->registerArgument('enabled', 'boolean', 'If FALSE, disables this Outlet', FALSE, FALSE);
	}

	/**
	 * @return void
	 */
	public function render() {
		$outlet = $this->createOutletFromArguments();
		$this->registerOutlet($outlet);
	}

	/**
	 * @param OutletInterface $outlet
	 * @return void
	 */
	protected function registerOutlet(OutletInterface $outlet) {
		$storage = $this->getStorage();
		if (FALSE === isset($storage['outlets'])) {
			$storage['outlets'] = array();
		}
		array_push($storage['outlets'], $outlet);
		$this->setStorage($storage);
	}

	/**
	 * @return OutletInterface
	 */
	protected function createOutletFromArguments() {
		if (TRUE === class_exists($this->outletClassName)) {
			$className = $this->outletClassName;
		} else {
			$className = 'FluidTYPO3\Fluidbackend\Outlet\\' . $this->outletClassName . 'Outlet';
		}
		/** @var $outlet OutletInterface */
		$outlet = $this->objectManager->get($className);
		$outlet->setName($this->arguments['name']);
		$outlet->setEnabled((boolean) $this->arguments['enabled']);
		$outlet->setLabel($this->arguments['label']);
		return $outlet;
	}

}
