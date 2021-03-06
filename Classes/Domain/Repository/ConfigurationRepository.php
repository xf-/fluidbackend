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
 * ### Module Configuration Repository
 *
 * Handles stored module configurations.
 *
 * @package Fluidbackend
 * @subpackage Domain\Repository
 */
class Tx_Fluidbackend_Domain_Repository_ConfigurationRepository extends Tx_Extbase_Persistence_Repository {

	/**
	 * @param string $name
	 * @param integer $storagePid
	 * @return Tx_Fluidbackend_Domain_Model_Configuration
	 */
	public function findOrCreateOneByNameInPid($name, $storagePid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setStoragePageIds(array($storagePid));
		$query->matching($query->logicalAnd(array($query->equals('name', $name), $query->equals('pid', $storagePid))));
		/** @var $candidate Tx_Fluidpages_Domain_Model_Configuration */
		$candidate = $query->execute()->getFirst();
		if (TRUE === empty($candidate)) {
			$candidate = $this->objectManager->get('Tx_Fluidbackend_Domain_Model_Configuration');
			$candidate->setName($name);
			$candidate->setLabel('AUTO: ' . $name);
			$candidate->setPid($storagePid);
			$this->add($candidate);
			$this->persistenceManager->persistAll();
		}
		return $candidate;
	}

}
