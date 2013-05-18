<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
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

/**
 * ### Outlet Execution Service
 *
 * Provides methods to cause Outlets to produce their
 * respective output as configured by each Outlet.
 *
 * @author Claus Due, Wildside A/S
 * @package Fluidbackend
 * @subpackage Service
 */
class Tx_Fluidbackend_Service_OutletService implements t3lib_Singleton {

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
	 * @param array|Tx_Fluidbackend_Outlet_OutletInterface $outletOrOutlets
	 * @param array $data
	 * @return void
	 * @throws Exception
	 */
	public function produce($outletOrOutlets, $data) {
		if (TRUE === $outletOrOutlets instanceof Tx_Fluidbackend_Outlet_OutletInterface) {
			$outletOrOutlets->produce($data);
		} else {
			foreach ($outletOrOutlets as $outlet) {
				$outlet->produce($data);
			}
		}
	}

}