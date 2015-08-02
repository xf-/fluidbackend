<?php
namespace FluidTYPO3\Fluidbackend\Test\Unit\Provider\Configuration;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Fluidbackend\Provider\Configuration\StorageConfigurationProvider;
use FluidTYPO3\Flux\Service\FluxService;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class StorageConfigurationProviderTest
 */
class StorageConfigurationProviderTest extends UnitTestCase {

	/**
	 * @param array $record
	 * @param string $getAction
	 * @param string $expected
	 * @test
	 * @dataProvider getControllerActionFromRecordTestValues
	 */
	public function testGetControllerActionFromRecord(array $record, $getAction, $expected) {
		/** @var StorageConfigurationProvider|\PHPUnit_Framework_MockObject_MockObject $mock */
		$mock = $this->getMock(
			'FluidTYPO3\\Fluidbackend\\Provider\\Configuration\\StorageConfigurationProvider',
			array('getExtensionKeyAndActionFromUrl')
		);
		$mock->expects($this->once())->method('getExtensionKeyAndActionFromUrl')->willReturn(array(NULL, $getAction));
		$result = $mock->getControllerActionFromRecord($record);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getControllerActionFromRecordTestValues() {
		return array(
			array(array(), NULL, NULL),
			array(array(), 'test2', 'test2'),
			array(array('name' => 'void-void-test2'), NULL, 'test2')
		);
	}

	/**
	 * @dataProvider getTemplatePathAndFilenameTestValues
	 * @param array $record
	 * @param string $expected
	 */
	public function testGetTemplatePathAndFilename(array $record, $expected) {
		/** @var FluxService|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('FluidTYPO3\\Flux\\Service\\FluxService', array('getViewConfigurationForExtensionName'));
		$service->expects($this->once())->method('getViewConfigurationForExtensionName')->with($expected);
		$instance = new StorageConfigurationProvider();
		$instance->injectConfigurationService($service);
		$result = $instance->getTemplatePathAndFilename($record);
		$this->assertNull($result);
	}

	/**
	 * @return array
	 */
	public function getTemplatePathAndFilenameTestValues() {
		return array(
			array(array(), ''),
			array(array('name' => 'foo-bar'), 'foo'),
			array(array('name' => 'Foo-Bar'), 'foo'),
		);
	}

	/**
	 * @return void
	 */
	public function testGetFlexFormValues() {
		$instance = new StorageConfigurationProvider();
		$result = $instance->getFlexFormValues(array());
		$this->assertArrayHasKey('record', $result);
	}

}
