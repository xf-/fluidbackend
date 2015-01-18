<?php
namespace FluidTYPO3\Fluidbackend\Outlet;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ## Outlet Definition: JSON file Outlet
 *
 * Writes JSON to a file.
 *
 * @package Fluidbackend
 * @subpackage Outlet
 */
class JsonOutlet extends AbstractOutlet implements OutletInterface {

	/**
	 * @var string
	 */
	protected $filePathAndFilename;

	/**
	 * Preserve HTML
	 *
	 * Uses optimal JSON encoding options to preserve HTML
	 * stored in field variables. The default behavior is
	 * to allow fields to contain HTML - if you do not wish
	 * this to be allowed, always force this value to FALSE
	 * in the ViewHelper which registers this Outlet.
	 *
	 * @var boolean
	 */
	protected $preserveHtml = TRUE;

	/**
	 * @var string
	 */
	protected $outlet = 'Json';

	/**
	 * @param string $filePathAndFilename
	 * @return void
	 */
	public function setFilePathAndFilename($filePathAndFilename) {
		$this->filePathAndFilename = $filePathAndFilename;
	}

	/**
	 * @return string
	 */
	public function getFilePathAndFilename() {
		return $this->filePathAndFilename;
	}

	/**
	 * @param boolean $preserveHtml
	 * @return void
	 */
	public function setPreserveHtml($preserveHtml) {
		$this->preserveHtml = $preserveHtml;
	}

	/**
	 * @return boolean
	 */
	public function getPreserveHtml() {
		return $this->preserveHtml;
	}

	/**
	 * @return \DateTime
	 */
	public function getModificationDate() {
		if (TRUE === file_exists($this->getFilePathAndFilename())) {
			return \DateTime::createFromFormat('U', filemtime($this->getFilePathAndFilename()));
		}
		return parent::getModificationDate();
	}

	/**
	 * @return string
	 */
	public function getTarget() {
		$filename = $this->getFilePathAndFilename();
		if (0 === strpos($filename, PATH_site)) {
			$filename = '[PATH_site]' . substr($filename, strlen(PATH_site));
		}
		return $filename;
	}

	/**
	 * @param array $data
	 * @return mixed
	 */
	public function produce(array $data) {
		if (TRUE === $this->getPreserveHtml()) {
			$json = json_encode($data, JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_QUOT);
		} else {
			$json = json_encode($data);
		}
        GeneralUtility::writeFile($this->getFilePathAndFilename(), $json);
		$this->message('JSON produced into file ' . $this->getFilePathAndFilename());
	}

}
