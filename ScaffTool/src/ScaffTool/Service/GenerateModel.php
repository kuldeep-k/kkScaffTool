<?php

namespace ScaffTool\Service;

use ScaffTool\Service\AbstractGenerateService;

class GenerateModel extends AbstractGenerateService
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
		//die('Debug');
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
		$modelName = $this->uModelName;
		$modelPath = $modulePath.'/src/'.$this->uModuleName.'/Model/'.$modelName.'.php';

		if(!file_exists(dirname($modelPath)))
        {
            //throw new \Exception('Model `'.dirname($modelPath).'` not exists ');
			mkdir(dirname($modelPath));
        }
        if(!is_writable(dirname($modelPath)))
        {
            throw new \Exception('Path `'.dirname($modelPath).'` is not writable.');
        }
		if(file_exists($modelPath))
        {
            throw new \Exception('Model `'.$modelName.'` already exists ');
        }

		$code = $this->getModelCode();
		//echo $modelPath;
		touch($modelPath);

		file_put_contents($modelPath, $code);


		// Add Model Table
		$modelName = $this->uModelName.'Table';
		$modelPath = $modulePath.'/src/'.$this->uModuleName.'/Model/'.$modelName.'.php';

		if(!file_exists(dirname($modelPath)))
        {
            throw new \Exception('Model `'.dirname($modelPath).'` not exists ');
        }
        if(!is_writable(dirname($modelPath)))
        {
            throw new \Exception('Path `'.dirname($modelPath).'` is not writable.');
        }
		if(file_exists($modelPath))
        {
            throw new \Exception('Model `'.$modelName.'` already exists ');
        }

		$code = $this->getModelTableCode();

		touch($modelPath);

		file_put_contents($modelPath, $code);
	}

	public function getModelTableCode()
	{
		$modelName = $this->uModelName;

		$code = '<?php ';
		$code .= $this->makeLine(2);
		$code .= 'namespace '.$this->uModuleName.'\\Model;';
		$code .= $this->makeLine(2);

		$code .= 'use Zend\Db\Sql\Sql;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Sql\Where;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Sql\Predicate;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Sql\Expression;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Sql\Statement;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Sql\Select;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\Adapter\Adapter;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\ResultSet\ResultSet;';
		$code .= $this->makeLine(1);
		$code .= 'use Zend\Db\TableGateway\AbstractTableGateway;';
		$code .= $this->makeLine(1);
		//$code .= 'use '.$this->moduleName.'\\Model\\'.$this->uModelName.';';
		//$code .= $this->makeLine(1);
		
		$code .= $this->makeLine(1);

		$code .= 'class '.$this->uModelName.'Table extends AbstractTableGateway';
		$code .= $this->makeLine(1);
		$code .= '{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1)."protected \$table ='".$this->tableName."';";		
		$code .= $this->makeLine(1);
	
		$code .= $this->makeTab(1)."public function __construct(Adapter \$adapter)";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$this->adapter = \$adapter;";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$this->resultSetPrototype = new ResultSet();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$this->resultSetPrototype->setArrayObjectPrototype(new ".$this->uModelName."());";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$this->initialize();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1)."public function fetchAll()";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$resultSet = \$this->select();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."return \$resultSet;";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1)."public function get".$this->uModelName."(\$id)";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$id  = (int) \$id;";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$rowset = \$this->select(array('".$this->primaryKeyColumn."' => \$id));";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$row = \$rowset->current();";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."if (!\$row) {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."throw new \\Exception(\"Could not find row \$id\");";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."}";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."return \$row;";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1)."public function save".$this->uModelName."(".$this->uModelName." \$".$this->modelName.")";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$data = array(";
		$code .= $this->makeLine(1);
		foreach($this->tableStructure as $fieldName => $fieldStructure)
		{
			$code .= $this->makeTab(3)."'".$fieldName."' => \$".$this->modelName."->".$fieldName.",";		
			$code .= $this->makeLine(1);
		}
		$code .= $this->makeTab(2).");";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$id = (int)\$".$this->modelName."->".$this->primaryKeyColumn.";";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."if (\$id == 0) {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."\$this->insert(\$data);";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."} else {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."if (\$this->get".$this->uModelName."(\$id)) {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."\$this->update(\$data, array('".$this->primaryKeyColumn."' => \$id));";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3)."} else {";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(4)."throw new \Exception('".$this->uModelName." id does not exist');";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(3).'}';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2).'}';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1)."public function delete".$this->uModelName."(\$id)";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."\$this->delete(array('".$this->primaryKeyColumn."' => \$id));";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= '}';
		$code .= $this->makeLine(1);
		return $code;
	}


	public function getModelCode()
	{
		$modelName = $this->uModelName;

		$code = '<?php ';
		$code .= $this->makeLine(2);
		$code .= 'namespace '.$this->uModuleName.'\\Model;';
		$code .= $this->makeLine(2);

		$code .= 'class '.$modelName;
		$code .= $this->makeLine(1);
		$code .= '{';
		$code .= $this->makeLine(1);
		
		foreach($this->tableStructure as $fieldName => $fieldStructure)
		{
			$code .= $this->makeTab(1)."public \$".$fieldName.';';		
			$code .= $this->makeLine(1);
		}

		$code .= $this->makeTab(1)."public function exchangeArray(\$data)";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);

		foreach($this->tableStructure as $fieldName => $fieldStructure)
		{
			$code .= $this->makeTab(2)."\$this->".$fieldName." = (isset(\$data['".$fieldName."'])) ? \$data['".$fieldName."'] : null; ";
			$code .= $this->makeLine(1);
		}

//$this->id     = (isset($data['id'])) ? $data['id'] : null;

		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= $this->makeTab(1).'public function getArrayCopy()';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'{';
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(2)."return get_object_vars(\$this);";
		$code .= $this->makeLine(1);
		$code .= $this->makeTab(1).'}';
		$code .= $this->makeLine(2);

		$code .= '}';
		$code .= $this->makeLine(1);
		return $code;
	}

}




