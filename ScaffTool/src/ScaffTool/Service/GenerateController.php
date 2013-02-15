<?php

namespace ScaffTool\Service;

use ScaffTool\Service\AbstractGenerateService;

class GenerateController extends AbstractGenerateService
{
	public function generate()
	{
		$configs = $this->getServiceLocator()->get('config');
		$base_path = $configs['BASE_PATH'];
		$modulePath = $base_path.'/module/'.ucfirst($this->moduleName);	
		if(!file_exists($modulePath))
		{
			throw new \Exception('Module Path `'.$modulePath.'` not exists.');
		}
		if(!is_writable($modulePath))
		{
			throw new \Exception('Module Path `'.$modulePath.'` is not writable.');
		}

		$controllerName = ucfirst($this->modelName).'Controller';
		$controllerPath = $modulePath.'/src/'.ucfirst($this->moduleName).'/Controller/'.$controllerName.'.php';

		if(!file_exists(dirname($controllerPath)))
        {
            throw new \Exception('Controller `'.dirname($controllerPath).'` not exists ');
        }
        if(!is_writable(dirname($controllerPath)))
        {
            throw new \Exception('Path `'.dirname($controllerPath).'` is not writable.');
        }
		if(file_exists($controllerPath))
        {
            throw new \Exception('Controller `'.$controllerName.'` already exists ');
        }

		$code = $this->getCode();

		touch($controllerPath);

		file_put_contents($controllerPath, $code);
	}

	public function getCode()
	{
		$controllerName = $this->uModelName.'Controller';

		$code = '<?php ';
		$code .= $this->makeLine(2);
		$code .= 'namespace '.$this->uModuleName.'\\Controller;';
		$code .= $this->makeLine(2);
		$code .= 'use Zend\\Mvc\\Controller\\AbstractActionController;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\\View\\Model\\ViewModel;';
		$code .= $this->makeLine(1);
		$code .= 'use '.$this->uModuleName.'\\Model\\'.$this->uModelName.';';
		$code .= $this->makeLine(1);
		$code .= 'use '.$this->uModuleName.'\\Form\\'.$this->uModelName.'Form;';
		$code .= $this->makeLine(1);

		$code .= 'class '.$controllerName.' extends AbstractActionController';
		$code .= $this->makeLine(1);
		$code .= '{';
		$code .= $this->makeLine(1);

		$code .= $this->makeTab(1).'public function indexAction()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2).'return new ViewModel(array(';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."'".$this->modelName."s' => \$this->getTable('".$this->tableName."')->fetchAll(),";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2).'));';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1).'public function addAction()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1).'public function editAction()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1).'public function deleteAction()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= '}';
		$code .= $this->makeLine(1);
		return $code;
	}

}

?>



