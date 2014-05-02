<?php

namespace ScaffTool\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Statement;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

use Zend\Db\Metadata\Metadata;

//class ScaffToolTable extends AbstractTableGateway
class ScaffToolTable
{
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        //$this->resultSetPrototype = new ResultSet();
        //$this->resultSetPrototype->setArrayObjectPrototype(new Album());
        //$this->initialize();
    }

    public function verifyTable($tableName)
    {
        try
        {
		    $metadata = new Metadata($this->adapter);	
        
		    $tableNames = $metadata->getTableNames();
        }
        catch(PDOException $e)
        {
            die('In');
        }
        //print_r($tableNames);die;
		if(!in_array($tableName, $tableNames))
		{
			throw new \Exception("Table `".$tableName."` not exists ");
		}
		return true;
		/*print_r($tableNames);die;
		$statement  = $this->adapter->query("SHOW TABLES LIKE ? ", array($tableName));
		$responseArray = $statement->toArray();
		if(sizeof($responseArray) == 0)
		{
			throw new \Exception("Table `".$tableName."` not exists ");
		}
        return $responseArray;*/
    }

	public function getTableStructure($tableName)	
	{
		$structure = array();
		$metadata = new Metadata($this->adapter);	
		$table = $metadata->getTable($tableName);			
		foreach ($table->getColumns() as $column) {
			$structure[$column->getName()] = array('type' => $column->getDataType(), 'primary' => false, 'foreign' => false);
    	}

		foreach ($metadata->getConstraints($tableName) as $constraint) {
        	/** @var $constraint Zend\Db\Metadata\Object\ConstraintObject */
            $constr_columns = $constraint->getColumns();
            $c_column = $constr_columns[0];
			if ($constraint->isPrimaryKey()) {
				
				if(sizeof($constr_columns) == 1)	
				{	
					
					$structure[$c_column]['primary'] = true;
				}

	        	if (!$constraint->hasColumns()) {
    	        	continue;
        		}
			}
            
	        //echo PHP_EOL.'            column: ' . implode(', ', $constraint->getColumns());
    	    if ($constraint->isForeignKey()) {
                //$foreign_column = $constr_columns[0];
        	    $fkCols = array();
            	foreach ($constraint->getReferencedColumns() as $refColumn) {
	                $structure[$c_column]['foreign'] = array($constraint->getReferencedTableName(), $refColumn);
    	        }
        	    //echo PHP_EOL.' => ' . implode(', ', $fkCols);
        	}
	        //echo PHP_EOL;

    	}
		return $structure;

	}
}

