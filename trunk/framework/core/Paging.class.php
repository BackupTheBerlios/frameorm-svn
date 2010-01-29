<?php 

class Paging
{
	protected $oSet;
	public $totalEntries;
	public $displayPerPage;
	public $totalPages;
	public $currentPage = 1;
	public $from;
	public $to;	
	
	public function __construct(SlicedObjectSet $set)
	{
		$this->oSet = $set;
		$this->totalEntries = $set->totalRows;
		$this->displayPerPage = count($set->range);
		$this->init();
	}
	
	public static function getSliceByPage($page, $perPage)
	{
		if(is_null($page) || $page == 1)
			return array(0, $perPage);
		return array(($page*$perPage)-$perPage, ($page*$perPage));
	}
	
	private function init()
	{
		$this->totalPages = floor($this->totalEntries/$this->displayPerPage);
		if ($this->totalEntries % $this->displayPerPage)
			$this->totalPages++;
		for($i = 0; $i < $this->totalEntries; $i+=$this->displayPerPage)
		{
			$max = $i+$this->displayPerPage;
			$slice = array($i, $i+$this->displayPerPage);
			if ($slice === $this->oSet->slice)
				break;
			$this->currentPage++;
		}
		$this->from = ($slice[0] == 0)?1:$slice[0]+1;
		if($slice[1] > $this->totalEntries)
			$this->to = $this->totalEntries;
		else
			$this->to = $slice[1]+1;
	}
	
	protected function getUrl($pageNumber)
	{
		$get = Context::getInstance()->request->get;
		if(is_null($get->page))
			return sprintf("?%s&page=%d", $get->queryString, $pageNumber);
		return "?".preg_replace("/page=([0-9])/", "page=$pageNumber", $get->queryString);
	}
	
	public function render()
	{
		if($this->displayPerPage < $this->totalEntries)
		{
			$i18n = Context::getInstance()->i18n;
			$page = '';
			if ($this->currentPage != 1){
				$prevLabel = $i18n->site->previous;
				$prev = $this->getUrl($this->currentPage-1);
				$page .= sprintf('<a class="search-previous" href="%s">%s</a>', 
									$prev, $prevLabel);
			}
			for($i = 1; $i <= $this->totalPages; $i++)
			{
				if ($i == $this->currentPage){
					$href = "#";
					$css = 'bold';
				}else{
					$href = $this->getUrl($i);
					$css = '';
				}
				$page .= sprintf('<a class="%s" href="%s">%d</a>', $css, $href, $i);
			}
			if ($this->currentPage != $this->totalPages){
				$nextLabel = $i18n->site->next;
				$prev = $this->getUrl($this->currentPage+1);
				$page .= sprintf('<a class="search-next" href="%s">%s</a>', 
									$prev, $nextLabel);
			}
			return $this->__getTemplate($page);
		}
	}
	
	private function __getTemplate($content)
	{
		$oContext = Context::getInstance();
		$oTemplate = new Template('site/searchPages.tpl.php');
		$oTemplate->pages = $content;
		return $oTemplate->parse();
	}
}

?>