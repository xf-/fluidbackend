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
 * ## Outlet Definition: Flash Message Outlet
 *
 * Uses TYPO3 Flash Messages as Outlet - simply
 * to inform the developer that a proper Outlet
 * has not yet been implemented.
 *
 * @package Fluidbackend
 * @subpackage Outlet
 */
class Tx_Fluidbackend_Outlet_FlashMessageOutlet extends Tx_Fluidbackend_Outlet_AbstractOutlet implements Tx_Fluidbackend_Outlet_OutletInterface {

	/**
	 * @var string
	 */
	protected $outlet = 'FlashMessage';

	/**
	 * @var integer
	 */
	protected $severity = t3lib_FlashMessage::OK;

	/**
	 * @var boolean
	 */
	protected $storeInSession = TRUE;

	/**
	 * @param array $data
	 * @return void
	 */
	public function produce(array $data) {
		$flashMessage = new t3lib_FlashMessage($this->getLabel() . ': <br /><pre>' . var_export($data, TRUE) . '</pre>',
			$this->getName(), $this->getSeverity(), TRUE);
		t3lib_FlashMessageQueue::addMessage($flashMessage);
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
