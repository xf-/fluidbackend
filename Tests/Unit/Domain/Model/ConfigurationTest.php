<?php
namespace FluidTYPO3\Fluidbackend\Tests\Unit\Domain\Model;

use FluidTYPO3\Fluidbackend\Domain\Model\Configuration;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends UnitTestCase {

	/**
	 * @dataProvider getGetterAndSetterTestValues
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$instance = new Configuration();
		$setter = 'set' . ucfirst($property);
		$getter = 'get' . ucfirst($property);
		$instance->$setter($value);
		$result = $instance->$getter();
		$this->assertEquals($value, $result);
	}

	/**
	 * @return array
	 */
	public function getGetterAndSetterTestValues() {
		return array(
			array('label', 'Test'),
			array('name', 'testname'),
			array('configuration', 'testconfiguration')
		);
	}

}
