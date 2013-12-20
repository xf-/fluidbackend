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
use FluidTYPO3\Fluidbackend\Outlet\JsonOutlet;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ## Outlet: JSON Outlet
 *
 * Produces JSON data into a specified file.
 *
 * @package Fluidbackend
 * @subpackage ViewHelpers\Outlet
 */
class JsonViewHelper extends AbstractOutletViewHelper {

	/**
	 * @var string
	 */
	protected $outletClassName = 'Json';

	/**
	 * @return void
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('path', 'string', 'Path, absolute/relative/EXT:-prefixed, of the desired destination filename', TRUE);
		$this->registerArgument('preserveHtml', 'boolean', 'If FALSE, does not protect HTML (by using optimal JSON encoding options) in the resulting output - which might break JSON consumers. By default this is switched on.', FALSE, TRUE);
	}

	/**
	 * @return OutletInterface
	 */
	protected function createOutletFromArguments() {
		$filePathAndFilename = GeneralUtility::getFileAbsFileName($this->arguments['path']);
		/** @var $outlet JsonOutlet */
		$outlet = parent::createOutletFromArguments();
		$outlet->setFilePathAndFilename($filePathAndFilename);
		$outlet->setPreserveHtml((boolean) $this->arguments['preserveHtml']);
		return $outlet;
	}

}
