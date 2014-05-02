<?php

namespace ScaffTool\Controller;

use ScaffTool\Model\ScaffToolTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
//use Zend\Math\Rand;

class ScaffToolController extends AbstractActionController
{
	public function indexAction()
	{
		echo 'inside Index';
		return "Ok";
	}

	public function createcrudAction() {
		try
		{
			$request = $this->getRequest();

			if (!$request instanceof ConsoleRequest){
    	        throw new \RuntimeException('You can only use this action from a console!');
        	}

			$tableName   = $request->getParam('tableName');
			$modelName   = $request->getParam('modelName');
			$moduleName   = $request->getParam('moduleName');

			$this->showLoader();
            
			$this->getServiceLocator()->get('ScaffTool\Model\ScaffToolTable')->verifyTable($tableName);

            $configGenerator = $this->getServiceLocator()->get('ScaffTool\Service\GenerateConfig');
			$configGenerator->setModule($moduleName);
			$configGenerator->setModel($modelName);
			$configGenerator->setTable($tableName);
			$configGenerator->generate();
            
			$controllerGenerator = $this->getServiceLocator()->get('ScaffTool\Service\GenerateController');
			$controllerGenerator->setModule($moduleName);
			$controllerGenerator->setModel($modelName);
			$controllerGenerator->setTable($tableName);
			$controllerGenerator->generate();
			
			$modelGenerator = $this->getServiceLocator()->get('ScaffTool\Service\GenerateModel');
			$modelGenerator->setModule($moduleName);
			$modelGenerator->setModel($modelName);
			$modelGenerator->setTable($tableName);
			$modelGenerator->generate();
			
			$viewGenerator = $this->getServiceLocator()->get('ScaffTool\Service\GenerateView');
			$viewGenerator->setModule($moduleName);
			$viewGenerator->setModel($modelName);
			$viewGenerator->setTable($tableName);
			$viewGenerator->generate();
			
			$formGenerator = $this->getServiceLocator()->get('ScaffTool\Service\GenerateForm');
			$formGenerator->setModule($moduleName);
			$formGenerator->setModel($modelName);
			$formGenerator->setTable($tableName);
			$formGenerator->generate();
            
			$this->hideLoader();
			
			//$controllerGenerator->setModel($modelName);
			//print_r($response);
		}
		catch(\Exception $e)
		{
			$this->hideLoader();
			return PHP_EOL.'Error : '.$e->getMessage().PHP_EOL;
		}
	}

	public function showLoader()
	{
		//echo 'Doing.........';
	}

	public function hideLoader()
	{
		echo PHP_EOL;
	}
}
