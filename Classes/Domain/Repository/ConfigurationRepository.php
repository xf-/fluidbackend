<?php
namespace FluidTYPO3\Fluidbackend\Domain\Repository;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\Repository;
use FluidTYPO3\Fluidbackend\Domain\Model\Configuration;

/**
 * ### Module Configuration Repository
 *
 * Handles stored module configurations.
 *
 * @package Fluidbackend
 * @subpackage Domain\Repository
 */
class ConfigurationRepository extends Repository {

	/**
	 * @param string $name
	 * @param integer $storagePid
	 * @return Configuration
	 */
	public function findOrCreateOneByNameInPid($name, $storagePid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setStoragePageIds(array($storagePid));
		$query->matching($query->logicalAnd(array($query->equals('name', $name), $query->equals('pid', $storagePid))));
		/** @var $candidate Configuration */
		$candidate = $query->execute()->getFirst();
		if (TRUE === empty($candidate)) {
			$candidate = $this->objectManager->get('FluidTYPO3\Fluidbackend\Domain\Model\Configuration');
			$candidate->setName($name);
			$candidate->setLabel('AUTO: ' . $name);
			$candidate->setPid($storagePid);
			$this->add($candidate);
			$this->persistenceManager->persistAll();
		}
		return $candidate;
	}

}
