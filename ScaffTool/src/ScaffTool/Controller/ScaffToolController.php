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

			echo $tableName   = $request->getParam('tableName');
			$modelName   = $request->getParam('modelName');
			$moduleName   = $request->getParam('moduleName');

			$this->getServiceLocator()->get('ScaffTool\Model\ScaffToolTable')->verifyTable($tableName);

			/*$controllerGenerator = $this->getServiceLocator()->get('ScaffTool\Service\GenerateController');
			$controllerGenerator->setModule($moduleName);
			$controllerGenerator->setModel($modelName);
			$controllerGenerator->setTable($tableName);
			$controllerGenerator->generate();
			*/
			$modelGenerator = $this->getServiceLocator()->get('ScaffTool\Service\GenerateModel');
			$modelGenerator->setModule($moduleName);
			$modelGenerator->setModel($modelName);
			$modelGenerator->setTable($tableName);
			$modelGenerator->generate();
			
			/*$controller_generator = $this->getServiceLocator()->get('ScaffTool\Service\GenerateController');
			$controllerGenerator->setModule($moduleName);
			$controllerGenerator->setModel($modelName);
			$controllerGenerator->generate();

			$controller_generator = $this->getServiceLocator()->get('ScaffTool\Service\GenerateController');
			$controllerGenerator->setModule($moduleName);
			$controllerGenerator->setModel($modelName);
			$controllerGenerator->generate();
*/
			//$controllerGenerator->setModel($modelName);
			print_r($response);
		}
		catch(\Exception $e)
		{
			return PHP_EOL.'Error : '.$e->getMessage().PHP_EOL;
		}
	}
}
