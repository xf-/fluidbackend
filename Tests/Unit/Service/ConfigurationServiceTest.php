<?php
namespace FluidTYPO3\Fluidbackend\Tests\Unit\Service;

use FluidTYPO3\Fluidbackend\Constants;
use FluidTYPO3\Fluidbackend\Service\ConfigurationService;
use FluidTYPO3\Flux\Core;
use FluidTYPO3\Flux\Form;
use FluidTYPO3\Flux\View\TemplatePaths;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ConfigurationServiceTest
 */
class ConfigurationServiceTest extends UnitTestCase {

	/**
	 * @dataProvider getRegisterModuleBasedOnFluxFormTestValues
	 * @param string $extension
	 * @param Form $form
	 * @param boolean $expectsException
	 */
	public function testRegisterModuleBasedOnFluxForm($extension, Form $form, $expectsException) {
		$instance = new ConfigurationService();
		Core::registerProviderExtensionKey($extension, 'Backend');
		if (TRUE === $expectsException) {
			$this->setExpectedException('RuntimeException');
		}
		$GLOBALS['TBE_MODULES'] = array('fake' => array());
		$instance->registerModuleBasedOnFluxForm($extension, $form);
		if (FALSE === $expectsException) {
			$this->assertNotEmpty($GLOBALS['TBE_MODULES']);
		}
		unset($GLOBALS['TBE_MODULES']);
	}

	/**
	 * @return array
	 */
	public function getRegisterModuleBasedOnFluxFormTestValues() {
		$formOkay1 = Form::create(array('options' => array('Fluidbackend' => array())));
		$formOkay2 = Form::create(array('options' => array('Fluidbackend' => array(
			Constants::FORM_OPTION_MODULE_GROUP => 'mygroup'
		))));
		$formOkay3 = Form::create(array('options' => array('Fluidbackend' => array(
			Constants::FORM_OPTION_MODULE_POSITION => 'top'
		))));
		$formOkay4 = Form::create(array('options' => array('Fluidbackend' => array(
			Constants::FORM_OPTION_MODULE_POSITION => 'bottom'
		))));
		$formOkay5 = Form::create(array('options' => array('Fluidbackend' => array(
			Constants::FORM_OPTION_MODULE_POSITION => 'after:web'
		))));
		$formOkay6 = Form::create(array('options' => array('Fluidbackend' => array(
			Constants::FORM_OPTION_MODULE_POSITION => 'before:fake'
		))));
		$formOkay7 = Form::create(array('options' => array('Fluidbackend' => array(
			Constants::FORM_OPTION_MODULE_POSITION => 'after:fake'
		))));
		$formOkay8 = Form::create(array('options' => array('Fluidbackend' => array(
			Constants::FORM_OPTION_MODULE_PAGE_TREE => TRUE
		))));
		return array(
			array('FluidTYPO3.Fluidbackend', $formOkay1, FALSE),
			array('FluidTYPO3.Fluidbackend', $formOkay2, FALSE),
			array('FluidTYPO3.Fluidbackend', $formOkay3, FALSE),
			array('FluidTYPO3.Fluidbackend', $formOkay4, FALSE),
			array('FluidTYPO3.Fluidbackend', $formOkay5, FALSE),
			array('FluidTYPO3.Fluidbackend', $formOkay6, FALSE),
			array('FluidTYPO3.Fluidbackend', $formOkay7, FALSE),
			array('FluidTYPO3.Fluidbackend', $formOkay8, FALSE),
			array('Invalid.Invalid', $formOkay1, TRUE),
		);
	}

	/**
	 * @return void
	 */
	public function testDetectAndRegisterAllFluidBackendModules() {
		$form = Form::create();
		$instance = $this->getMock(
			'FluidTYPO3\\Fluidbackend\\Service\\ConfigurationService',
			array('getBackendModuleTemplatePaths', 'getFormFromTemplateFile', 'registerModuleBasedOnFluxForm')
		);
		$paths = new TemplatePaths('FluidTYPO3.Fluidbackend');
		$instance->expects($this->once())->method('getBackendModuleTemplatePaths')->willReturn(array(
			'FluidTYPO3.Fluidbackend' => $paths->toArray()
		));
		$instance->expects($this->atLeastOnce())->method('registerModuleBasedOnFluxForm');
		$instance->expects($this->atLeastOnce())->method('getFormFromTemplateFile')->willReturn($form);
		$instance->detectAndRegisterAllFluidBackendModules();
	}

}
