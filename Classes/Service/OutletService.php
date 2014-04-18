<?php
namespace FluidTYPO3\Fluidbackend\Service;
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
 ***************************************************************/
use TYPO3\CMS\Core\SingletonInterface;
use FluidTYPO3\Fluidbackend\Outlet\OutletInterface;

/**
 * ### Outlet Execution Service
 *
 * Provides methods to cause Outlets to produce their
 * respective output as configured by each Outlet.
 *
 * @author Claus Due
 * @package Fluidbackend
 * @subpackage Service
 */
class OutletService implements SingletonInterface {

	/**
	 * @param mixed $post
	 * @param integer $level
	 * @return mixed
	 */
	public function trimLanguageWrappersFromPostedData($post, $level = 0) {
		foreach ($post as $name => $value) {
			if (($name === 'v' && $level === 0)) {
				return $this->trimLanguageWrappersFromPostedData(array_pop($value), $level + 1);
			} elseif ($name === 'vDEF' || $name === 'lDEF') {
				return $this->trimLanguageWrappersFromPostedData($value, $level + 1);
			} else {
				$post[$name] = $this->trimLanguageWrappersFromPostedData($value, $level + 1);
			}
		}
		return $post;
	}

	/**
	 * @param OutletInterface[]|OutletInterface $outletOrOutlets
	 * @param array $data
	 * @return void
	 * @throws \Exception
	 */
	public function produce($outletOrOutlets, $data) {
		if (TRUE === $outletOrOutlets instanceof OutletInterface) {
			$outletOrOutlets->produce($data);
		} else {
			foreach ($outletOrOutlets as $outlet) {
				$outlet->produce($data);
			}
		}
	}

}