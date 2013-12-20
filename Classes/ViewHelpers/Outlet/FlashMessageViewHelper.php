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
use FluidTYPO3\Fluidbackend\Outlet\FlashMessageOutlet;
use TYPO3\CMS\Core\Messaging\FlashMessage;

/**
 * ## Outlet: Flash Message
 *
 * Produces information about the output as a standard
 * TYPO3 FlashMessage.
 *
 * @package Fluidbackend
 * @subpackage ViewHelpers\Outlet
 */
class FlashMessageViewHelper extends AbstractOutletViewHelper {

	/**
	 * @var string
	 */
	protected $outletClassName = 'FlashMessage';

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('severity', 'integer', 'Optional severity / log level - defaults to t3lib_FlashMessage::OK', FALSE, FlashMessage::OK);
		$this->registerArgument('storeInSession', 'boolean', 'If FALSE, only stores the flash message for this page load (which means it MUST be read by for example your data post processors). Default is to save in session.', FALSE, TRUE);
	}

	/**
	 * @return OutletInterface
	 */
	protected function createOutletFromArguments() {
		/** @var $outlet FlashMessageOutlet */
		$outlet = parent::createOutletFromArguments();
		$outlet->setSeverity($this->arguments['severity']);
		$outlet->setStoreInSession((boolean) $this->arguments['storeInSession']);
		return $outlet;
	}

}
