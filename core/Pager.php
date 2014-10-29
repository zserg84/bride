<?php
require_once("core/IPager.php");

class Pager implements IPager{

	private $_pagination;

	public $prevPageLabel = '<';
	public $nextPageLabel = '>';

	public $pageCssClass = 'page';

	public function __construct($pagination){
		$this->_pagination = $pagination;
	}

	public function renderPager(){
//		var_dump($_SERVER);
		$buttons = $this->createPageButtons();
		if(empty($buttons))
			return;
		echo '<div class="pager">';
			echo '<ul>';
			foreach($buttons as $button){
				echo $button;
			}
			echo '</ul>';
		echo '</div>';
	}

	protected function createPageButtons()
	{
		$pagination = $this->_pagination;
		$pageCount=$pagination->getPageCount();
		if($pageCount <= 1)
			return array();

		$currentPage=$pagination->getCurrentPage(false);
		$buttons=array();

		if(($page=$currentPage-1)<0)
			$page=0;
		$buttons[]=$this->createPageButton($this->prevPageLabel,$page,'',$currentPage<=0,false);

		if($pageCount > 10){
			$dot = '...';
			$buttons[]=$this->createPageButton('1',0,$this->pageCssClass,false,$currentPage==0);
			$buttons[]=$this->createPageButton('2',1,$this->pageCssClass,false,$currentPage==1);

			if($currentPage >= 5){
				$buttons[]=$dot;
				$beginPage = $currentPage - 2;
				if($currentPage <= $pageCount - 5)
					$endPage = $currentPage + 2;
				else
					$endPage = $pageCount - 3;
			}
			else{
				$beginPage = 2;
				$endPage = $currentPage + 2;
			}
		}

		for($i=$beginPage;$i<=$endPage;++$i){
			$buttons[]=$this->createPageButton($i+1,$i,$this->pageCssClass,false,$i==$currentPage);
		}

		if($pageCount > 10){
			if($currentPage < $pageCount - 5 && $pageCount > 10 && $currentPage == $endPage - 2){
				$buttons[]=$dot;
			}
			$buttons[]=$this->createPageButton($pageCount - 1,$pageCount - 2,$this->pageCssClass,false, $pageCount - 2==$currentPage);
			$buttons[]=$this->createPageButton($pageCount,$pageCount - 1,$this->pageCssClass,false, $pageCount - 1==$currentPage);
		}

		if(($page=$currentPage+1)>=$pageCount-1)
			$page=$pageCount-1;

		$buttons[]=$this->createPageButton($this->nextPageLabel,$page,$this->pageCssClass,$currentPage>=$pageCount-1,false);
		return $buttons;
	}

	protected function createPageButton($label,$page,$class,$hidden,$selected)
	{
		$class .= $selected ? ' selected' : '';
		return '<li class="'.$class.'">'.$this->createPageUrl($label,++$page).'</li>';
//		var_dump($_SERVER);
	}

	protected function createPageUrl($label, $page, $delimiter1 = '&', $delimiter2 = '='){
		$pagination = $this->_pagination;
		$params = $_REQUEST;
//		if(!$params){
//			$params = $pagination->pageVar . '='. $page;
//		}
		require_once("core/Helper.php");
		$params = Helper::urlParamsReplace($params, array($pagination->pageVar=>$page), $delimiter1, $delimiter2);
		$url = $_SERVER['PHP_SELF'];
		if($params){
			$url .= '?' . $params;
		}
		$link = '<a href="'.$url.'">'.$label.'</a>';
		return $link;
	}

}