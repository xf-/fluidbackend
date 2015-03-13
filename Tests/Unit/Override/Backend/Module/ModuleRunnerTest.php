<?php
namespace FluidTYPO3\Fluidbackend\Test\Unit\Override\Backend\Module;

use FluidTYPO3\Fluidbackend\Override\Backend\Module\ModuleLoader;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ModuleRunnerTest
 */
class ModuleRunnerTest extends UnitTestCase {

	/**
	 * @test
	 */
	public function testLoad() {
		$loader = $this->getMock('FluidTYPO3\Fluidbackend\Override\Backend\Module\ModuleRunner', array('performModuleCall'));
		$loader->expects($this->once())->method('performModuleCall')->with('test');
		$loader->callModule('test');
	}

}
