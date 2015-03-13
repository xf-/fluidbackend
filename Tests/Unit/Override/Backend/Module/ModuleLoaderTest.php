<?php
namespace FluidTYPO3\Fluidbackend\Test\Unit\Override\Backend\Module;

use FluidTYPO3\Fluidbackend\Override\Backend\Module\ModuleLoader;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ModuleLoaderTest
 */
class ModuleLoaderTest extends UnitTestCase {

	/**
	 * @test
	 */
	public function testLoad() {
		$loader = $this->getMock('FluidTYPO3\Fluidbackend\Override\Backend\Module\ModuleLoader', array('performModuleLoading'));
		$loader->expects($this->once())->method('performModuleLoading');
		$loader->load(array());
	}

}
