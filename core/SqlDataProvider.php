<?php
require_once("core/DataProvider.php");
class SqlDataProvider extends DataProvider{

	private $_db;

	public $sql;
	public $params = array();

	public function __construct($sql, $params)
	{
		foreach($params as $key=>$value)
			$this->$key=$value;

		$this->sql=$sql;
	}

	public function setDb($value)
	{
		$this->_db=$value;
	}

	public function getDb(){
		return $this->_db;
	}

	protected function fetchData(){
		if(($pagination=$this->getPagination())!==false)
		{
			$pagination->setItemCount($this->getTotalItemCount());
			$limit=$pagination->getLimit();
			$offset=$pagination->getOffset();
			$sql = 'SELECT t.* FROM(' . $this->sql . ') as t LIMIT '.$offset.', '.$limit;
			$db = $this->db;
			$stmt = $db->prepare($sql);
			$stmt->execute($this->params);
			return $stmt->fetchAll();
		}
	}
}