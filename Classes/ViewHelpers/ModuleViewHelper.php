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
 * ### Module configuration VieWHelper
 *
 * Sets various aspects of how this module should be integrated.
 * Use inside flux:flexform to configure the module contained
 * within the Flux form.
 *
 * @package Fluidbackend
 * @subpackage ViewHelpers
 */
class Tx_Fluidbackend_ViewHelpers_ModuleViewHelper extends Tx_Flux_Core_ViewHelper_AbstractFlexformViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('group', 'string', 'Parent module, for example "web" or "info" - or your own custom module name, in which case the module is added after the module indicated in the "after" attribute', FALSE, 'web');
		$this->registerArgument('position', 'string', 'Name of the (parent-level) group after which the group used by this module should be added - fx "top", "after:help" or "before:web"', FALSE, 'bottom');
		$this->registerArgument('navigation', 'boolean', 'If FALSE, suppresses uses of the page tree navigation component', FALSE, TRUE);
	}

	/**
	 * @return void
	 */
	public function render() {
		$storage = $this->getStorage();
		$storage['moduleGroup'] = $this->arguments['group'];
		$storage['modulePosition'] = $this->arguments['position'];
		$storage['modulePageTree'] = (boolean) $this->arguments['navigation'];
		$this->setStorage($storage);
	}

}
