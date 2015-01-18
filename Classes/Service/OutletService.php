<?php
namespace FluidTYPO3\Fluidbackend\Service;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
