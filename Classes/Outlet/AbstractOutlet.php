<?php
namespace FluidTYPO3\Fluidbackend\Outlet;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;

/**
 * ### Outlet Definition
 *
 * Defines one data outlet for a Fluid backend form.
 * Each outlet is updated with the information when
 * the form is saved. Created by the OutletViewHelper.
 *
 * @package Fluidbackend
 * @subpackage Outlet
 */
abstract class AbstractOutlet implements OutletInterface {

	/**
	 * @var boolean
	 */
	protected $enabled = TRUE;

	/**
	 * If set to FALSE in a subclass, $settings will NOT
	 * expanded to a hierarchy when field names use dots.
	 * Default behavior is to deepen $settings this way.
	 *
	 * @var boolean
	 */
	protected $deepenSettings = TRUE;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var mixed
	 */
	protected $target;

	/**
	 * @var \DateTime
	 */
	protected $modificationDate;

	/**
	 * @var string
	 */
	protected $outlet = 'ConfigurationRecord';

	/**
	 * @param boolean $enabled
	 * @return void
	 */
	public function setEnabled($enabled) {
		$this->enabled = $enabled;
	}

	/**
	 * @return boolean
	 */
	public function getEnabled() {
		return $this->enabled;
	}

	/**
	 * @param string $name
	 * @return void
 	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $label
	 * @return void
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param mixed $target
	 * @return void
	 */
	public function setTarget($target) {
		$this->target = $target;
	}

	/**
	 * @return mixed
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * @param string $outlet
	 * @return void
	 * @throws \Exception
	 */
	public function setOutlet($outlet) {
		if (FALSE === class_exists('FluidTYPO3\Fluidbackend\Outlet\\' . $outlet . 'Outlet')) {
			if (FALSE === class_exists($outlet)) {
				throw new \Exception('Outlet type"' . $outlet . '" was neither a fully qualified class name nor a name of a ' .
					'built-in Processor (as FluidTYPO3\Fluidbackend\Outlet\<strong>Identity</strong>Outlet', 1368317785);
			}
		}
		$this->outlet = $outlet;
	}

	/**
	 * @return string
	 */
	public function getOutlet() {
		return $this->outlet;
	}

	/**
	 * @param \DateTime $modificationDate
	 * @return void
	 */
	public function setModificationDate($modificationDate) {
		$this->modificationDate = $modificationDate;
	}

	/**
	 * @return \DateTime
	 */
	public function getModificationDate() {
		if (!$this->modificationDate) {
			return \DateTime::createFromFormat('U', 0);
		}
		return $this->modificationDate;
	}

	/**
	 * @return boolean
	 */
	public function assertDeepenSettings() {
		return $this->deepenSettings;
	}

	/**
	 * @param $message
	 * @param integer $severity
	 * @return void
	 */
	protected function message($message, $severity = FlashMessage::OK) {
		$flashMessage = new FlashMessage($message, $this->getName() . ': ' . $this->getLabel(), $severity, TRUE);
        FlashMessageQueue::addMessage($flashMessage);
	}

}
