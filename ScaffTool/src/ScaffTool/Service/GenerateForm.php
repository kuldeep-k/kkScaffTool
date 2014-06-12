<?php

namespace ScaffTool\Service;

use ScaffTool\Service\AbstractGenerateService;
use Zend\Config\Reader\Xml;

class GenerateForm extends AbstractGenerateService
{
	public $primaryKeyColumn;
	public $tableStructure;
	public function generate()
	{
            
		$configs = $this->getServiceLocator()->get('config');

		$this->tableStructure = $this->getServiceLocator()->get('ScaffTool\Model\ScaffToolTable')->getTableStructure($this->tableName);

		foreach($this->tableStructure as $fieldName => $fieldStructure)
		{
			if($fieldStructure['primary'] === true)
			{
				$this->primaryKeyColumn = $fieldName;
			}
		}

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
        
		// Add Model
		$formName = $this->uModelName;
		$formPath = $modulePath.'/src/'.$this->uModuleName.'/Form/'.$formName.'Form.php';

		if(!file_exists(dirname($formPath)))
        {
            //throw new \Exception('Path `'.dirname($formPath).'` not exists ');
			mkdir(dirname($formPath));
        }
        if(!is_writable(dirname($formPath)))
        {
            throw new \Exception('Path `'.dirname($formPath).'` is not writable.');
        }
		if(file_exists($formPath))
        {
            throw new \Exception('Form `'.$formName.'` already exists ');
        }

		$code = $this->getFormCode();
		//echo $formPath;
		touch($formPath);

		file_put_contents($formPath, $code);

	}

	public function getFormCode()
	{
		$modelName = $this->uModelName;

		$code = '<?php ';
		$code .= $this->makeLine(2);
		$code .= 'namespace '.$this->uModuleName.'\\Form;';
		$code .= $this->makeLine(2);

		$code .= 'use Zend\Form\Form;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\InputFilter\Factory;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\InputFilter\InputFilter;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\InputFilter\InputFilterAwareInterface;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\InputFilter\InputFilterInterface;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Adapter\AdapterInterface;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Adapter\Adapter;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Sql\Sql;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Sql\Select;';
		$code .= $this->makeLine(1);

		//$code .= 'use '.$this->moduleName.'\\Model\\'.$this->uModelName.';';
		//$code .= $this->makeLine(1);
		
		$code .= $this->makeLine(1);

		$code .= 'class '.$this->uModelName.'Form extends Form implements InputFilterAwareInterface';
		$code .= $this->makeLine(1);
		$code .= '{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1)."protected \$inputFilter;";		
		$code .= $this->makeLine(1);
	
		$code .= $this->makeTab(1)."public function __construct(AdapterInterface \$dbAdapter)";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."parent::__construct('".strtolower($modelName)."');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$this->setAttribute('method', 'post');";
		$code .= $this->makeLine(1);

		$code .= $this->makeTab(2)."\$this->add(array(";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."'name' => 'id',";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."'attributes' => array(";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."'type'  => 'hidden',";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."),";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."));";
		$code .= $this->makeLine(2);

		foreach($this->tableStructure as $fieldName => $fieldStructure)
		{
			if($fieldName != $this->primaryKeyColumn)
			{
				    $code .= $this->makeLine(1);
				    $code .= $this->makeTab(2)."\$this->add(array(";
				    $code .= $this->makeLine(1);
				    $code .= $this->makeTab(3)."'name' => '".$fieldName."',";
				    $code .= $this->makeLine(1);
				    $code .= $this->makeTab(3)."'attributes' => array(";
				    $code .= $this->makeLine(1);
				    $code .= $this->makeTab(4)."'type'  => 'text',";
				    $code .= $this->makeLine(1);
				    $code .= $this->makeTab(3)."),";
				    $code .= $this->makeLine(1);
				    $code .= $this->makeTab(3)."'options' => array(";
				    $code .= $this->makeLine(1);
				    $code .= $this->makeTab(4)."'label'  => '".$this->convertToLabel($fieldName)."',";
				    $code .= $this->makeLine(1);
				    $code .= $this->makeTab(3)."),";
				    $code .= $this->makeLine(1);
				    $code .= $this->makeTab(2)."));";
				    $code .= $this->makeLine(2);
			}
		}

		$code .= $this->makeTab(2)."\$this->add(array(";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."'name' => 'submit',";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."'attributes' => array(";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."'type'  => 'submit',";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."'value'  => 'Save',";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."'id'  => 'submit',";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."),";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."));";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= '}';
		$code .= $this->makeLine(1);
		return $code;
	}

}




