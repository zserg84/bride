<?php

/*
 * Пагинатор
 * */
class Pagination {

	const DEFAULT_PAGE_SIZE=20;
	const DEFAULT_PAGER_CLASS = 'Pager';

	public $pageVar='page';

	/*
	 * Элементов на странице
	 * */
	private $_pageSize = self::DEFAULT_PAGE_SIZE;

	/*
	 * количество элементов в выборке
	 * */
	private $_itemCount = 0;
	/*
	 * номер текущей страницы
	 * */
	private $_currentPage;
	/*
	 * Класс пагинатора
	 * */
	private $_pager = self::DEFAULT_PAGER_CLASS;

	public function __construct($itemCount=0)
	{
		$this->setItemCount($itemCount);
	}

	public function getPageSize()
	{
		return $this->_pageSize;
	}

	public function setPageSize($pageSize)
	{
		if(($this->_pageSize=$pageSize)<=0)
			$this->_pageSize=self::DEFAULT_PAGE_SIZE;
	}

	public function getItemCount()
	{
		return $this->_itemCount;
	}

	public function setItemCount($itemCount)
	{
		if(($this->_itemCount=$itemCount)<0)
			$this->_itemCount=0;
	}

	public function getLimit()
	{
		return $this->getPageSize();
	}

	public function getPageCount()
	{
		return (int)(($this->_itemCount+$this->_pageSize-1)/$this->_pageSize);
	}

	public function getCurrentPage()
	{
		if(!$this->_currentPage)
		{
			if(isset($_GET[$this->pageVar]))
			{
				$this->_currentPage=(int)$_GET[$this->pageVar]-1;
				$pageCount=$this->getPageCount();
				if($this->_currentPage>=$pageCount)
					$this->_currentPage=$pageCount-1;
			}
			else
				$this->_currentPage=0;
		}
		return $this->_currentPage;
	}

	public function getOffset()
	{
		return $this->getCurrentPage()*$this->getPageSize();
	}

	public function getPager(){
		$pager = $this->_pager;
		if(class_exists($pager))
			return new $pager($this);
		else
			throw new Exception('Класс '.$pager.' не объявлен');
	}

	public function setPager($value ){
		$this->_pager = $value;
	}
}