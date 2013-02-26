<?php

namespace ScaffTool\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractGenerateService implements ServiceLocatorAwareInterface {
	public $modelName;
	public $moduleName;
	public $tableName;
	public $uModuleName;
	public $uModelName;
    protected $service_manager;

	public function setModel($modelName)
	{
		$this->modelName = $modelName;
		$this->uModelName = ucfirst($modelName);
	}

	public function setModule($moduleName)
	{
		$this->moduleName = $moduleName;
		$this->uModuleName = ucfirst($moduleName);
	}

	public function setTable($tableName)
	{
		$this->tableName = $tableName;
	}

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->service_manager = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->service_manager;
    }

	protected function makeLine($numOfLines)
	{
		return str_repeat(PHP_EOL, $numOfLines);
	}

	protected function makeTab($numOfTabs)
	{
		return str_repeat("\t", $numOfTabs);
	}

	public function convertToLabel($text)
	{
		return ucwords(str_replace('_', ' ', $text));
	}
}


?>
