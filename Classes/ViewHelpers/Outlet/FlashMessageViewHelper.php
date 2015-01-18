<?php
namespace FluidTYPO3\Fluidbackend\ViewHelpers\Outlet;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
