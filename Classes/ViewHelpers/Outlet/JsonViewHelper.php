<?php
namespace FluidTYPO3\Fluidbackend\ViewHelpers\Outlet;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
