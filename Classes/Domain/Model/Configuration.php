<?php
namespace FluidTYPO3\Fluidbackend\Domain\Model;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * ### Module Configuration Model
 *
 * Stores configurations set in Fluidbackend modules.
 *
 * @package Fluidbackend
 * @subpackage Domain\Model
 */
class Configuration extends AbstractEntity {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var string
	 */
	protected $configuration;

	/**
	 * @param string $configuration
	 * @return void
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * @return mixed
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * @param string $label
	 * @return void
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

}
