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
 * ## Outlet Definition: JSON file Outlet
 *
 * Writes JSON to a file.
 *
 * @package Fluidbackend
 * @subpackage Outlet
 */
class Tx_Fluidbackend_Outlet_JsonOutlet extends Tx_Fluidbackend_Outlet_AbstractOutlet implements Tx_Fluidbackend_Outlet_OutletInterface {

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
	 * @return DateTime
	 */
	public function getModificationDate() {
		if (TRUE === file_exists($this->getFilePathAndFilename())) {
			return DateTime::createFromFormat('U', filemtime($this->getFilePathAndFilename()));
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
		t3lib_div::writeFile($this->getFilePathAndFilename(), $json);
		$this->message('JSON produced into file ' . $this->getFilePathAndFilename());
	}

}
