<?php

namespace ScaffTool\Service;

use ScaffTool\Service\AbstractGenerateService;

class GenerateConfig extends AbstractGenerateService
{
	public function generate()
	{
		$configs = $this->getServiceLocator()->get('config');
		$base_path = $configs['BASE_PATH'];
		$modulePath = $base_path.'/module/'.$this->uModuleName;	
		if(!file_exists($modulePath))
		{
			throw new \Exception('Module Path `'.$modulePath.'` not exists.');
		}
		if(!is_writable($modulePath))
		{
			throw new \Exception('Module Path `'.$modulePath.'` is not writable.');
		}

        $configName = 'module.'.strtolower($this->modelName).'.php';
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
        $template = str_replace(array('--CMODULE--', '--CMODEL--', '--MODEL--'), array($this->uModuleName, $this->uModelName, $this->lModelName), $template);
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
	
}

?>
