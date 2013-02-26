<?php

namespace ScaffTool\Service;

use ScaffTool\Service\AbstractGenerateService;

class GenerateView extends AbstractGenerateService
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

		$modulePath = $base_path.'/module/'.ucfirst($this->moduleName);	
		if(!file_exists($modulePath))
		{
			throw new \Exception('Module Path `'.$modulePath.'` not exists.');
		}
		if(!is_writable($modulePath))
		{
			throw new \Exception('Module Path `'.$modulePath.'` is not writable.');
		}

		// Add Listing index view
		$viewPath = $modulePath.'/view/'.strtolower($this->moduleName).'/'.strtolower($this->modelName).'/index.phtml';

		if(!file_exists(dirname($viewPath)))
        {
			mkdir(dirname($viewPath));
            //throw new \Exception('Path `'.dirname($viewPath).'` not exists ');
        }
        if(!is_writable(dirname($viewPath)))
        {
            throw new \Exception('Path `'.dirname($viewPath).'` is not writable.');
        }
		if(file_exists($viewPath))
        {
            throw new \Exception('View `'.$viewPath.'` already exists ');
        }

		$code = $this->getIndexViewCode();

		touch($viewPath);

		file_put_contents($viewPath, $code);


		$viewPath = $modulePath.'/view/'.strtolower($this->moduleName).'/'.strtolower($this->modelName).'/add.phtml';
		if(file_exists($viewPath))
        {
            throw new \Exception('View `'.$viewPath.'` already exists ');
        }

		$code = $this->getAddViewCode();

		touch($viewPath);

		file_put_contents($viewPath, $code);
	}

	public function getIndexViewCode()
	{
		$modelName = $this->uModelName;
		$lModelName = strtolower($this->modelName);

		$code = "<?php \$this->headTitle('My ".$modelName."s'); ?>";
		$code .= $this->makeLine(1);
		$code .= "<h1><?php echo \$this->escapeHtml('My ".$modelName."s'); ?></h1>";
		$code .= $this->makeLine(1);

		$code .= "<p><a href=\"<?php echo \$this->url('".$lModelName."', array('action'=>'add'));?>\">Add new ".$modelName."</a></p>";
		$code .= $this->makeLine(1);
		$code .= "<table class=\"table\">";
		$code .= $this->makeLine(1);
		$code .= '<tr>';
		$code .= $this->makeLine(1);
		foreach($this->tableStructure as $fieldName => $fieldStructure)
		{
			$code .= "<td>".$this->convertToLabel($fieldName)."</td>";
			$code .= $this->makeLine(1);
		}
		$code .= '</tr>';
		$code .= $this->makeLine(1);
		$code .= "<?php foreach(\$".$lModelName."s as \$".$lModelName.") : ?>";
		$code .= $this->makeLine(1);
		$code .= "<tr style=\"background-color:<?php echo \$this->cycle(array('#F0F0F0','#FFFFFF'))->next() ?>\" >";
		$code .= $this->makeLine(1);


		foreach($this->tableStructure as $fieldName => $fieldStructure)
		{
			//$code .= $this->makeTab(3)."'".$fieldName."' => \$".$this->modelName."->".$fieldName.",";		
			//$code .= $this->makeLine(1);
			$code .= "<td><?php echo \$this->escapeHtml(\$".$lModelName."->".$fieldName.");?></td>";
			$code .= $this->makeLine(1);
		}

		/*$code .= "<td><?php echo \$this->escapeHtml(\$".$lModelName."->title);?></td>";
		$code .= $this->makeLine(1);
		$code .= "<td><?php echo \$this->escapeHtml(\$".$lModelName."->artist);?></td>";
		$code .= $this->makeLine(1);*/
		$code .= "<td><a href=\"<?php echo \$this->url('".$lModelName."',array('action'=>'edit', 'id' => \$".$lModelName."->".$this->primaryKeyColumn."));?>\">Edit</a>";
		
		$code .= $this->makeLine(1);
		$code .= "<a href=\"<?php echo \$this->url('".$lModelName."',array('action'=>'delete', 'id' => \$".$lModelName."->".$this->primaryKeyColumn."));?>\">Delete</a></td>";
		$code .= $this->makeLine(1);
		$code .= '</tr>';
		$code .= $this->makeLine(1);
		$code .= '<?php endforeach; ?>';
		$code .= $this->makeLine(1);
		$code .= '</table>';
		$code .= $this->makeLine(1);
		return $code;
	}

	public function getAddViewCode()
	{
		$modelName = $this->uModelName;
		$lModelName = strtolower($this->modelName);

		$code = "<?php \$this->headTitle('Add new ".$modelName."'); ?>";
		$code .= $this->makeLine(1);
		$code .= "<h1><?php echo \$this->escapeHtml('Add new ".$modelName."'); ?></h1>";
		$code .= $this->makeLine(1);
		$code .= "<?php";
		$code .= $this->makeLine(1);
		$code .= "\$form = \$this->form;";
		$code .= $this->makeLine(1);
		$code .= "\$form->setAttribute('action', \$this->url('".$lModelName."', array('action' => 'add')));";
		$code .= $this->makeLine(1);
		$code .= "\$form->prepare();";
		$code .= $this->makeLine(1);

		$code .= "echo \$this->form()->openTag(\$form);";
		$code .= $this->makeLine(1);
		$code .= "echo \$this->formHidden(\$form->get('".$this->primaryKeyColumn."'));";
		$code .= $this->makeLine(1);
		foreach($this->tableStructure as $fieldName => $fieldStructure)
		{
			$code .= "echo \$this->formRow(\$form->get('".$fieldName."'));";
			$code .= $this->makeLine(1);
		}
		$code .= "echo \$this->formSubmit(\$form->get('submit'));";
		$code .= $this->makeLine(1);
		$code .= "echo \$this->form()->closeTag();";
		$code .= $this->makeLine(1);
		return $code;
	}

	public function getEditViewCode()
	{
		$modelName = $this->uModelName;
		$lModelName = strtolower($this->modelName);

		$code = "<?php \$this->headTitle('Edit ".$modelName."'); ?>";
		$code .= $this->makeLine(1);
		$code .= "<h1><?php echo \$this->escapeHtml('Edit ".$modelName."'); ?></h1>";
		$code .= $this->makeLine(1);
		$code .= "<?php";
		$code .= $this->makeLine(1);
		$code .= "\$form = \$this->form;";
		$code .= $this->makeLine(1);
		$code .= "\$form->setAttribute('action', \$this->url('".$lModelName."', array('action' => 'edit', '".$this->primaryKeyColumn."'     => \$this->".$this->primaryKeyColumn.")));";
		$code .= $this->makeLine(1);
		$code .= "\$form->prepare();";
		$code .= $this->makeLine(1);

		$code .= "echo \$this->form()->openTag(\$form);";
		$code .= $this->makeLine(1);
		$code .= "echo \$this->formHidden(\$form->get('".$this->primaryKeyColumn."'));";
		$code .= $this->makeLine(1);
		foreach($this->tableStructure as $fieldName => $fieldStructure)
		{
			$code .= "echo \$this->formRow(\$form->get('".$fieldName."'));";
			$code .= $this->makeLine(1);
		}
		$code .= "echo \$this->formSubmit(\$form->get('submit'));";
		$code .= $this->makeLine(1);
		$code .= "echo \$this->form()->closeTag();";
		$code .= $this->makeLine(1);
		return $code;
	}

}




