<?php
namespace FluidTYPO3\Fluidbackend\ViewHelpers\Outlet;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
use FluidTYPO3\Fluidbackend\Outlet\TypoScriptOutlet;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ### Outlet Definition: TypoScript Settings Array Storage
 *
 * Defines a TypoScript target (`sys_template` or file) in
 * in which to store the data posted from the form. The name
 * of the Outlet is used to uniquely identify the TypoScript
 * and the label is used as name (left out when writing files).
 *
 * @package Fluidbackend
 * @subpackage ViewHelpers\Outlet
 */
class TypoScriptViewHelper extends AbstractOutletViewHelper {

	/**
	 * @var string
	 */
	protected $outletClassName = 'TypoScript';

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('storagePid', 'integer', 'Optional storage PID (if not this current page)', FALSE, -1);
		$this->registerArgument('path', 'string', 'If filled, writes TypoScript to this file (if path is a directory, an automatic setup.txt or constants.txt file is created, depending on $constants). Takes precedence over $storagePid', FALSE, NULL);
		$this->registerArgument('constants', 'boolean', 'If TRUE, writes TypoScript constants instead of setup. Default is to write setup', FALSE, FALSE);
	}

	/**
	 * @return OutletInterface
	 */
	protected function createOutletFromArguments() {
		/** @var $outlet TypoScriptOutlet */
		$outlet = parent::createOutletFromArguments();
		if (0 > $this->arguments['storagePid']) {
			$storagePid = GeneralUtility::_GET('id');
		} else {
			$storagePid = $this->arguments['storagePid'];
		}
		$outlet->setPath($this->arguments['path']);
		$outlet->setConstants((boolean) $this->arguments['constants']);
		$outlet->setStoragePid($storagePid);
		return $outlet;
	}

}
