<?php
namespace FluidTYPO3\Fluidbackend\Outlet;
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
 * ## Outlet Processor Interface
 *
 * Implemented by all OutletProcessor types.
 *
 * @package Fluidbackend
 * @subpackage Outlet
 */
interface OutletInterface {

	/**
	 * @param boolean $enabled
	 * @return void
	 * @abstract
	 */
	public function setEnabled($enabled);

	/**
	 * @return boolean
	 * @abstract
	 */
	public function getEnabled();

	/**
	 * @param string $name
	 * @return void
	 * @abstract
	 */
	public function setName($name);

	/**
	 * @return string
	 * @abstract
	 */
	public function getName();

	/**
	 * @param string $label
	 * @return void
	 * @abstract
	 */
	public function setLabel($label);

	/**
	 * @return string
	 * @abstract
	 */
	public function getLabel();

	/**
	 * @param mixed $target
	 * @return void
	 * @abstract
	 */
	public function setTarget($target);

	/**
	 * @return mixed
	 * @abstract
	 */
	public function getTarget();

	/**
	 * @param string $outlet
	 * @return void
	 * @throws \Exception
	 * @abstract
	 */
	public function setOutlet($outlet);

	/**
	 * @return string
	 * @abstract
	 */
	public function getOutlet();

	/**
	 * @param \DateTime $modificationDate
	 * @return void
	 * @abstract
	 */
	public function setModificationDate($modificationDate);

	/**
	 * @return \DateTime
	 * @abstract
	 */
	public function getModificationDate();

	/**
	 * @param array $data
	 * @return mixed
	 * @abstract
	 */
	public function produce(array $data);

	/**
	 * @return boolean
	 * @abstract
	 */
	public function assertDeepenSettings();
}
