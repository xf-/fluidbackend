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
 * ## Outlet Definition: Flash Message Outlet
 *
 * Uses TYPO3 Flash Messages as Outlet - simply
 * to inform the developer that a proper Outlet
 * has not yet been implemented.
 *
 * @package Fluidbackend
 * @subpackage Outlet
 */
class FlashMessageOutlet extends AbstractOutlet implements OutletInterface {

	/**
	 * @var string
	 */
	protected $outlet = 'FlashMessage';

	/**
	 * @var integer
	 */
	protected $severity = FlashMessage::OK;

	/**
	 * @var boolean
	 */
	protected $storeInSession = TRUE;

	/**
	 * @param array $data
	 * @return void
	 */
	public function produce(array $data) {
		$flashMessage = new FlashMessage($this->getLabel() . ': <br /><pre>' . var_export($data, TRUE) . '</pre>',
			$this->getName(), $this->getSeverity(), TRUE);
        FlashMessageQueue::addMessage($flashMessage);
	}

	/**
	 * @param integer $severity
	 * @return void
	 */
	public function setSeverity($severity) {
		$this->severity = $severity;
	}

	/**
	 * @return integer
	 */
	public function getSeverity() {
		return $this->severity;
	}

	/**
	 * @param boolean $storeInSession
	 * @return void
	 */
	public function setStoreInSession($storeInSession) {
		$this->storeInSession = $storeInSession;
	}

	/**
	 * @return boolean
	 */
	public function getStoreInSession() {
		return $this->storeInSession;
	}

}
