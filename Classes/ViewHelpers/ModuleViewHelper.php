<?php
namespace FluidTYPO3\Fluidbackend\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Flux\ViewHelpers\AbstractformViewHelper;

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
class ModuleViewHelper extends AbstractformViewHelper {

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
		$form = $this->getForm();
		$storage = (array) $form->getOption('fluidbackend');
		$storage['moduleGroup'] = $this->arguments['group'];
		$storage['modulePosition'] = $this->arguments['position'];
		$storage['modulePageTree'] = (boolean) $this->arguments['navigation'];
		$form->setOption('fluidbackend', $storage);
	}

}
