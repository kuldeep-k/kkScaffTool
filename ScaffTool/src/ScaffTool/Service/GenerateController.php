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
		$code .= $this->makeTab(1)."public \$".$this->modelName."Table;";
		$code .= $this->makeLine(1);

		$code .= $this->makeTab(1).'public function indexAction()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2).'return new ViewModel(array(';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."'".strtolower($this->modelName)."s' => \$this->getTable()->fetchAll(),";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2).'));';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1).'public function addAction()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$form = new ".$this->uModelName."Form();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$form->get('submit')->setValue('Add');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$request = \$this->getRequest();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."if (\$request->isPost()) {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."\$".strtolower($this->modelName)." = new ".$this->uModelName."();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."\$form->setInputFilter(\$form->getInputFilter());";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."\$form->setData(\$request->getPost());";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."if (\$form->isValid()) {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."\$".strtolower($this->modelName)."->exchangeArray(\$form->getData());";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."\$this->getTable()->save".$this->uModelName."(\$".strtolower($this->modelName).");";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."\$this->flashMessenger()->addMessage('Thank you for your comment!');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."return \$this->redirect()->toRoute('".strtolower($this->modelName)."');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."}";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."}";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."return array('form' => \$form);";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1).'public function editAction()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$id = (int) \$this->params()->fromRoute('id', 0);";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."if (!\$id) {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."return \$this->redirect()->toRoute('".strtolower($this->modelName)."', array(";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."'action' => 'add'";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."));";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."}";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$".strtolower($this->modelName)." = \$this->getTable()->get".$this->uModelName."(\$id);";
		$code .= $this->makeLine(1);


		$code .= $this->makeTab(2)."\$form = new ".$this->uModelName."Form();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$form->bind(\$".strtolower($this->modelName).");";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$form->get('submit')->setAttribute('value', 'Edit');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$request = \$this->getRequest();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."if (\$request->isPost()) {";
		$code .= $this->makeLine(1);
		//$code .= $this->makeTab(3)."\$".strtolower($this->modelName)." = new ".$this->uModelName."();";
		//$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."\$form->setInputFilter(\$form->getInputFilter());";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."\$form->setData(\$request->getPost());";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."if (\$form->isValid()) {";
		$code .= $this->makeLine(1);
		//$code .= $this->makeTab(4)."\$".strtolower($this->modelName)."->exchangeArray(\$form->getData());";
		//$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."\$this->getTable()->save".$this->uModelName."(\$form->getData());";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."\$this->flashMessenger()->addMessage('Thank you for your comment!');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."return \$this->redirect()->toRoute('".strtolower($this->modelName)."');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."}";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."}";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."return array('form' => \$form, 'id' => \$id);";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1).'public function deleteAction()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$id = (int) \$this->params()->fromRoute('id', 0);";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."if (!\$id) {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."return \$this->redirect()->toRoute('".strtolower($this->modelName)."');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."}";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$id = (int) \$this->params()->fromRoute('id', 0);";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$this->getTable()->delete".$this->uModelName."(\$id);";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."return \$this->redirect()->toRoute('".strtolower($this->modelName)."');";
		$code .= $this->makeLine(1);



		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1).'public function getTable()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."if (!\$this->".$this->modelName."Table) {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."\$sm = \$this->getServiceLocator();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."\$this->".$this->modelName."Table = \$sm->get('".$this->moduleName."\\Model\\".$this->uModelName."Table');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."}";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."return \$this->".$this->modelName."Table;";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= '}';
		$code .= $this->makeLine(1);
		return $code;
	}

}

?>



