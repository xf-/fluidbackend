<?php
namespace FluidTYPO3\Fluidbackend\Tests\Unit\Domain\Repository;

use FluidTYPO3\Fluidbackend\Domain\Model\Configuration;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

/**
 * Class ConfigurationRepositoryTest
 */
class ConfigurationRepositoryTest extends UnitTestCase {

	/**
	 * @test
	 */
	public function testFindOrCreateOneByNameInPidReturnsFound() {
		$instance = $this->getInstanceMock(FALSE);
		$result = $instance->findOrCreateOneByNameInPid('foobar', 1);
	}

	/**
	 * @test
	 */
	public function testFindOrCreateOneByNameInPidReturnsCreated() {
		$instance = $this->getInstanceMock(TRUE);
		$result = $instance->findOrCreateOneByNameInPid('foobar', 1);
	}

	/**
	 * @param boolean $expectsAdd
	 * @return ConfigurationRepository
	 */
	protected function getInstanceMock($expectsAdd) {
		$methods = array('createQuery');
		$configuration = new Configuration();
		$objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManager', array('get'));
		$persistenceManager = $this->getMock(
			'TYPO3\\CMS\\Extbase\\Persistence\\PersistenceManager',
			array('persistAll'),
			array(), '', FALSE
		);
		if (TRUE === $expectsAdd) {
			$methods[] = 'add';
		}
		$instance = $this->getMock(
			'FluidTYPO3\\Fluidbackend\\Domain\\Repository\\ConfigurationRepository',
			$methods,
			array(), '', FALSE
		);
		$this->inject($instance, 'persistenceManager', $persistenceManager);
		$this->inject($instance, 'objectManager', $objectManager);
		$query = $this->getMock(
			'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Query',
			array('execute', 'matching', 'equals', 'logicalAnd'),
			array(), '', FALSE
		);
		$query->setQuerySettings(
			GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')
				->get('\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings')
		);
		$result = $this->getMock(
			'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult',
			array('getFirst'),
			array(), '', FALSE
		);
		$query->expects($this->once())->method('execute')->willReturn($result);
		$instance->expects($this->once())->method('createQuery')->willReturn($query);
		if (TRUE === $expectsAdd) {
			$result->expects($this->once())->method('getFirst')->willReturn(NULL);
			$objectManager->expects($this->once())->method('get')->willReturn($configuration);
			$persistenceManager->expects($this->once())->method('persistAll');
			$instance->expects($this->once())->method('add')->with($configuration);
		} else {
			$result->expects($this->once())->method('getFirst')->willReturn($configuration);
		}
		return $instance;
	}

}
