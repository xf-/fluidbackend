<?php
namespace FluidTYPO3\Fluidbackend\Outlet;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
