<?php
namespace FluidTYPO3\Fluidbackend\Tests\Unit\Controller;

use FluidTYPO3\Fluidbackend\Domain\Model\Configuration;
use FluidTYPO3\Flux\Form;
use FluidTYPO3\Flux\Outlet\OutletInterface;
use FluidTYPO3\Flux\Outlet\StandardOutlet;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Request;
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
	 * @test
	 */
	public function testGetCurrentConfigurationNameReturnsRequestParameterIfActionIsSave() {
		$request = new Request();
		$request->setControllerActionName('save');
		$request->setArguments(array('configurationName' => 'foobar'));
		$instance = $this->getMockForAbstractClass('FluidTYPO3\\Fluidbackend\\Controller\\AbstractBackendController');
		$this->inject($instance, 'request', $request);
		$result = $this->callInaccessibleMethod($instance, 'getCurrentConfigurationName');
		$this->assertEquals('foobar', $result);
	}

	/**
	 * @test
	 */
	public function testGetCurrentOutlet() {
		$GLOBALS['TYPO3_DB'] = $this->getMock(
			'TYPO3\\CMS\\Core\\Database\\DatabaseConnection',
			array('exec_SELECTquery', 'sql_fetch_assoc', 'exec_INSERTquery')
		);
		$form = Form::create();
		$request = new Request();
		$provider = $this->getMock('FluidTYPO3\\Flux\\Provider\\Provider', array('getForm'));
		$provider->expects($this->once())->method('getForm')->willReturn($form);
		$instance = $this->getMockForAbstractClass('FluidTYPO3\\Fluidbackend\\Controller\\AbstractBackendController');
		$this->inject($instance, 'provider', $provider);
		$this->inject($instance, 'request', $request);
		$result = $this->callInaccessibleMethod($instance, 'getCurrentOutlet');
		$this->assertInstanceOf('FluidTYPO3\\Flux\\Outlet\\OutletInterface', $result);
	}

	/**
	 * @test
	 */
	public function testRenderActionDoesNothing() {
		$instance = $this->getMockForAbstractClass('FluidTYPO3\\Fluidbackend\\Controller\\AbstractBackendController');
		$result = $instance->renderAction();
		$this->assertNull($result);
	}

	/**
	 * @dataProvider getSaveActionTestValues
	 * @param OutletInterface $outlet
	 * @param array $settings
	 * @param boolean $expectsException
	 */
	public function testSaveAction(OutletInterface $outlet, array $settings, $expectsException) {
		$instance = $this->getMockForAbstractClass(
			'FluidTYPO3\\Fluidbackend\\Controller\\AbstractBackendController',
			array(), '', FALSE, FALSE, TRUE,
			array(
				'getCurrentOutlet',
				'getRecord',
				'getFluxTableName',
				'getFluxRecordField',
				'getCurrentConfigurationName',
				'redirect'
			)
		);
		$repository = $this->getMock(
			'FluidTYPO3\\Fluidbackend\\Domain\\Repository\\ConfigurationRepository',
			array('findOrCreateOneByNameInPid', 'update'),
			array(), '', FALSE
		);
		$repository->expects($this->any())->method('findOrCreateOneByNameInPid')->willReturn(new Configuration());
		$instance->injectConfigurationRepository($repository);
		$instance->expects($this->once())->method('getCurrentOutlet')->willReturn($outlet);
		$instance->expects($this->any())->method('getRecord')->willReturn(array());
		$instance->expects($this->once())->method('getFluxTableName')->willReturn('table');
		$instance->expects($this->once())->method('getFluxRecordField')->willReturn('field');
		if (TRUE === $expectsException) {
			$this->setExpectedException('RuntimeException');
		}
		$instance->saveAction(array('table' => array(123 => array('field' => array('data' => $settings)))), 'test');
	}

	/**
	 * @return array
	 */
	public function getSaveActionTestValues() {
		$okOutlet = new StandardOutlet();
		$badOutlet = $this->getMock(get_class($okOutlet), array('produce'));
		$badOutlet->expects($this->once())->method('produce')->willThrowException(new \RuntimeException('test'));
		return array(
			array($okOutlet, array(), FALSE),
			array($okOutlet, array('v' => array('vDEF' => array('bar' => 'test'))), FALSE),
			array($okOutlet, array('v' => array('vDEF' => array('foo.bar' => 'test'))), FALSE),
			array($badOutlet, array(), TRUE),
		);
	}

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
