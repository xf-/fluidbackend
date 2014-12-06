<?php
namespace FluidTYPO3\Fluidbackend\Test\Unit\Provider\Configuration;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
 ***************************************************************/

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
