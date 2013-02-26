<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ScaffTool;

use Zend\Mvc\ModuleRouteListener;

use Zend\Mvc\MvcEvent;

use ScaffTool\Model\ScaffToolTable;

use Zend\Console\Adapter\AdapterInterface as Console;

//use Zend\Log\Logger;
//use Zend\Log\Writer\Stream;
//class Module implements AutoloaderProviderInterface, ConfigProviderInterface, BootstrapListenerInterface
class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
	  
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

	public function getConsoleUsage(Console $console){
        return array(
            // Describe available commands
            'scafftool createcrud <moduleName> <tableName> <modelName>'    => 'Create a CRUD structure modelName for tableName Under moduleNmae',

            // Describe expected parameters
            array( 'tableName',            'Table Name used to be analyze fields' ),
            array( 'modelName',            'Model Name to be created' ),
            array( 'moduleName',            'Module Name under which model to be created' ),
            array( '--verbose|-v',     '(optional) turn on verbose mode'        ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                /*'Zend\Log\Logger' =>  function($sm) {
                    $logger = new Zend\Log\Logger;
                    $writer = new Zend\Log\Writer\Stream('./data/log/'.date('Y-m-d').'-error.log');
                    $logger->addWriter($writer);
                    return $logger;
                },*/
				'DbAdapter' =>  function($sm) {
                    return $sm->get('Zend\Db\Adapter\Adapter');
                },
				'ScaffTool\Service\GenerateController'	=> function($sm) {
					return new Service\GenerateController;
				},
				'ScaffTool\Service\GenerateModel'	=> function($sm) {
					return new Service\GenerateModel;
				},
				'ScaffTool\Service\GenerateView'	=> function($sm) {
					return new Service\GenerateView;
				},
				'ScaffTool\Service\GenerateForm'	=> function($sm) {
					return new Service\GenerateForm;
				},
				'ScaffTool\Model\ScaffToolTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new Model\ScaffToolTable($dbAdapter);
                    return $table;
                },

            ),
        );
    }
}
