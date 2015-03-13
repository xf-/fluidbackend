<?php
namespace FluidTYPO3\Fluidbackend\Tests\Unit\Controller;

use FluidTYPO3\Fluidbackend\Domain\Model\Configuration;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class AbstractBackendControllerTest
 */
class AbstractBackendControllerTest extends UnitTestCase {

	/**
	 * @var string
	 */
	protected $class = 'FluidTYPO3\\Fluidbackend\\Controller\\AbstractBackendController';

	/**
	 * @return void
	 */
	public function testInitializeView() {
		$view = $this->getMock('TYPO3\\CMS\\Fluid\\View\\StandaloneView', array('setControllerContext'));
		$view->expects($this->once())->method('setControllerContext')->with($this->anything());
		$configuration = new Configuration();
		$formEngine = $this->getMock('TYPO3\\CMS\\Backend\\Form\\FormEngine', array('initDefaultBEmode'), array(), '', FALSE);
		$objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManager', array('get'));
		$objectManager->expects($this->atLeastOnce())->method('get')->willReturn($formEngine);
		$repository = $this->getMock(
			'FluidTYPO3\\Fluidbackend\\Domain\\Repository\\ConfigurationRepository',
			array('findOrCreateOneByNameInPid'),
			array(), '', FALSE
		);
		$repository->expects($this->once())->method('findOrCreateOneByNameInPid')->willReturn($configuration);
		$instance = $this->getMockForAbstractClass(
			$this->class,
			array(), '', FALSE, FALSE, TRUE,
			array(
				'getCurrentConfigurationName',
				'getRecord',
				'initializeProvider',
				'initializeSettings',
				'initializeOverriddenSettings',
				'initializeViewObject',
				'initializeViewVariables'
			)
		);
		$this->inject($instance, 'controllerContext', new ControllerContext());
		$instance->injectObjectManager($objectManager);
		$instance->injectConfigurationRepository($repository);
		$instance->initializeView($view);
	}

}
