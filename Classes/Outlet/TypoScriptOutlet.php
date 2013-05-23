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
 * ## Outlet Definition: TypoScript Outlet
 *
 * Saves the posted data as a TypoScript array
 * either in the $storagePid or $path. If $path
 * is used, a static file is written (if saving
 * $constants the file is named "constants.txt"
 * otherwise "setup.txt"). If saving to a
 * $storagePid, $constants determines which field
 * is used to store the TypoScript array and
 * therefore which type of settings it is.
 *
 * @package Fluidbackend
 * @subpackage Outlet
 */
class Tx_Fluidbackend_Outlet_TypoScriptOutlet extends Tx_Fluidbackend_Outlet_AbstractOutlet implements Tx_Fluidbackend_Outlet_OutletInterface {

	/**
	 * Default changed to FALSE which allows this Outlet
	 * to recieve a flat (using dotted field names) array
	 * of data rather than a deep hierarchy. This eases
	 * writing of TypoScript quite a bit.
	 *
	 * @var boolean
	 */
	protected $deepenSettings = FALSE;

	/**
	 * @var integer
	 */
	protected $storagePid;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var boolean
	 */
	protected $constants = FALSE;

	/**
	 * @var string
	 */
	protected $outlet = 'TypoScript';

	/**
	 * @param integer $storagePid
	 * @return void
	 */
	public function setStoragePid($storagePid) {
		$this->storagePid = $storagePid;
	}

	/**
	 * @return integer
	 */
	public function getStoragePid() {
		return $this->storagePid;
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param boolean $constants
	 * @return void
	 */
	public function setConstants($constants) {
		$this->constants = $constants;
	}

	/**
	 * @return boolean
	 */
	public function getConstants() {
		return $this->constants;
	}

	/**
	 * @return string
	 */
	public function getTarget() {
		$name = $this->getName();
		$label = $this->getLabel();
		return '"' . $name . ': ' . $label . '"';
	}

	/**
	 * @return DateTime
	 */
	public function getModificationDate() {
		$storagePid = $this->getStoragePid();
		$name = $this->getName();
		$label = $this->getLabel();
		$clause = "pid = '" . $storagePid . "' AND title = '" . $name . ': ' . $label . "'";
		$record = $this->getOrCreateRecord($clause);
		return DateTime::createFromFormat('U', $record['tstamp']);
	}

	/**
	 * @param array $data
	 * @return void
	 */
	public function produce(array $data) {
		$storagePid = $this->getStoragePid();
		$name = $this->getName();
		$label = $this->getLabel();
		$clause = "pid = '" . $storagePid . "' AND title = '" . $name . ': ' . $label . "'";
		$record = $this->getOrCreateRecord($clause);
		$typoScript = '';
		$lines = 0;
		foreach ($data as $sheetName => $fieldSet) {
			foreach ($fieldSet as $fieldName => $fieldValue) {
				$typoScript .= $sheetName . '.' . $fieldName . ' = ' . strval($fieldValue) . LF;
				++ $lines;
			}
		}
		if (TRUE === $this->getConstants()) {
			$record['constants'] = $typoScript;
		} else {
			$record['config'] = $typoScript;
		}
		$record['tstamp'] = time();
		if (TRUE === isset($record['uid'])) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery('sys_template', $clause . " AND uid = '" . $record['uid'] . "'", $record);
			$destination = 'sys_template:' . $record['uid'];
		} else {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('sys_template', $record);
			$destination = 'new sys_template record';
		}
		$this->message('TypoScript (' . $lines . ' lines) written to ' . $destination . ' (' . $name . ': ' . $label . ') on pid ' . $storagePid);
	}

	/**
	 * @param string $clause
	 * @return array
	 */
	protected function getOrCreateRecord($clause) {
		$storagePid = $this->getStoragePid();
		$name = $this->getName();
		$label = $this->getLabel();
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'sys_template', $clause);
		$record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
		if (TRUE === empty($record)) {
			$record = array(
				'pid' => $storagePid,
				'title' => $name . ': ' . $label,
				'crdate' => time(),
				'tstamp' => time()
			);
		}
		return $record;
	}

}
