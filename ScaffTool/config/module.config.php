<?php

// module/Album/conﬁg/module.conﬁg.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'ScaffTool\Controller\ScaffTool' => 'ScaffTool\Controller\ScaffToolController',
        ),
    ),
   // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            /*'album' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/album[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Album\Controller\Album',
                        'action'     => 'index',
                    ),
                ),
            ),*/
        ),
    ),
	'console' => array(
        'router' => array(
            'routes' => array(
                // Console routes go here
				'create-crud' => array(
                    'options' => array(
                        'route'    => 'scafftool createcrud <moduleName> <tableName> <modelName> ',
                        'defaults' => array(
                            'controller' => 'ScaffTool\Controller\ScaffTool',
                            'action'     => 'createcrud'
                        )
                    )
                )
            )
        )
    ),
	'BASE_PATH' => '/var/www/zend2'
);

