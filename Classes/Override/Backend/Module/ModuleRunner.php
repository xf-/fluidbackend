<?php
namespace FluidTYPO3\Fluidbackend\Override\Backend\Module;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Template\DocumentTemplate;

/**
 * Class ModuleRunner
 */
class ModuleRunner extends \TYPO3\CMS\Extbase\Core\ModuleRunner {

	/**
	 * @param string $moduleSignature
	 * @return boolean
	 */
	public function callModule($moduleSignature) {
		/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		/** @var $configurationService \FluidTYPO3\Fluidbackend\Service\ConfigurationService */
		$configurationService = $objectManager->get('FluidTYPO3\Fluidbackend\Service\ConfigurationService');
		$configurationService->detectAndRegisterAllFluidBackendModules();
		return parent::callModule($moduleSignature);
	}

}
