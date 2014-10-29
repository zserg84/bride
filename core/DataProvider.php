<?php
require_once("core/IDataProvider.php");
abstract class DataProvider implements IDataProvider{

	private $_id;
	private $_data;
	private $_pagination;
	private $_totalItemCount;

	abstract protected function fetchData();

	public function getId()
	{
		return $this->_id;
	}

	public function setId($value)
	{
		$this->_id=$value;
	}

	public function __get($name)
	{
		$getter='get'.$name;
		if(method_exists($this,$getter))
			return $this->$getter();
		elseif(isset($this->$name))
		{
			return $this->$name;
		}

		throw new Exception('Свойство "'.__CLASS__.'.'.$name.'" не объявлено');
	}

	public function __set($name,$value)
	{
		$setter='set'.$name;
		if(method_exists($this,$setter))
			return $this->$setter($value);
		elseif(isset($this->$name))
		{
			return $this->$name = $value;
		}

		throw new Exception('Свойство "'.__CLASS__.'.'.$name.'" не объявлено');
	}

	public function getPagination($className='Pagination')
	{
		if($this->_pagination===null)
		{
			require_once("core/{$className}.php");
			$this->_pagination=new $className;
		}
		return $this->_pagination;
	}

	public function setPagination($value)
	{
		if(is_array($value))
		{
			if(isset($value['class']))
			{
				$pagination=$this->getPagination($value['class']);
				unset($value['class']);
			}
			else
				$pagination=$this->getPagination();

			foreach($value as $k=>$v)
				$pagination->$k=$v;
		}
		else
			$this->_pagination=$value;
	}

	public function getData()
	{
		if($this->_data===null)
			$this->_data=$this->fetchData();
		return $this->_data;
	}

	public function setData($value)
	{
		$this->_data=$value;
	}

	public function getItemCount()
	{
		return count($this->getData());
	}

	public function getTotalItemCount()
	{
		return $this->_totalItemCount;
	}

	public function setTotalItemCount($value)
	{
		$this->_totalItemCount=$value;
	}
}