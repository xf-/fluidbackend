<?php
namespace FluidTYPO3\Fluidbackend\Test\Unit\Provider\Configuration;

/*
 * This file is part of the FluidTYPO3/Fluidbackend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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

}
