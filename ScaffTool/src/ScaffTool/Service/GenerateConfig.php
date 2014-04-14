<?php

namespace ScaffTool\Service;

use ScaffTool\Service\AbstractGenerateService;

class GenerateConfig extends AbstractGenerateService
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

        $configName = $this->modelName.'.config.php';
		//$controllerName = ucfirst($this->modelName).'Controller';
		$configPath = $modulePath.'/config/'.$configName;

		if(!file_exists(dirname($configPath)))
        {
            throw new \Exception('Path `'.dirname($configPath).'` not exists ');
        }
        if(!is_writable(dirname($configPath)))
        {
            throw new \Exception('Path `'.dirname($configPath).'` is not writable.');
        }
		if(file_exists($configPath))
        {
            throw new \Exception('Config `'.$configPath.'` already exists ');
        }

        $template = $this->getTemplate();
        $template = str_replace(array('--CMODULE--', '--CMODEL--', '--MODEL--'), array($this->uModuleName, $this->uModelName, $this->modelName), $template);
		//$code = $this->getCode();

		touch($configPath);

		file_put_contents($configPath, $template);
	}

    public function getTemplate()
    {
        return $template = "<?php
    return array(
    'controllers' => array(
        'invokables' => array(
            '--CMODULE--\Controller\--CMODEL--' => '--CMODULE--\Controller\--CMODEL--Controller',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            '--MODEL--' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/--MODEL--[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => '--CMODULE--\Controller\--CMODEL--',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
);";
    }
	public function getCode()
	{
		$controllerName = $this->uModelName.'Controller';

		$code = '<?php ';
		$code .= $this->makeLine(2);
		$code .= 'return array(';
		$code .= $this->makeLine(2);
		$code .= $this->makeTab(1)."'controllers' => array(";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."'invokables' => array(";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3).$this->uModuleName."\\Controller\\".$this->uModuleName."' => '".$this->uModuleName."\\Controller\\".$this->uModuleName."Controller";
		$code .= $this->makeTab(2)."),";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1)."),";
		$code .= $this->makeLine(1);

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
		$code .= $this->makeTab(2)."\$id = (int) new ".$this->uModelName."Form();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$form = new ".$this->uModelName."Form();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$form->bind(\$".strtolower($this->modelName).");";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$form->get('submit')->setValue('Edit');";
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
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1).'public function deleteAction()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
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
		$code .= $this->makeTab(3)."\$this->".$this->modelName."Table = \$sm->get('".$this->moduleName."\\Model\\".$this->modelName."Table');";
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



