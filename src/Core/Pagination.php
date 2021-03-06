<?php

namespace Plasticode\Core;

class Pagination {
    protected $linker;
    protected $renderer;
    
    public function __construct($linker, $renderer)
    {
        $this->linker = $linker;
        $this->renderer = $renderer;
    }

	public function complex($url, $count, $index, $pageSize)
	{
		$paging = [];
		$pages = [];
		
		$stepping = 1;
		$neighbours = 7;
		
		if ($count > $pageSize) {
			// prev page
			if ($index > 1) {
        		$prev = $this->page($url, $index - 1, false, $this->renderer->prev(), 'Предыдущая страница');
        		$pages[] = $prev;
        		$paging['prev'] = $prev;
			}

			$pageCount = ceil($count / $pageSize);
			
			$shownIndex = 1;
			$step = ceil($pageCount / $stepping);

			while ($shownIndex <= $pageCount) {
				if ($shownIndex == 1 ||
					$shownIndex >= $pageCount ||
					($shownIndex % $step == 0) ||
					(abs($shownIndex - $index) <= $neighbours)) {
					$pages[] = $this->page($url, $shownIndex, $shownIndex == $index);
				}
				
				$shownIndex++;
			}

			// next page
			if ($index < $pageCount) {
				$next = $this->page($url, $index + 1, false, $this->renderer->next(), 'Следующая страница');
				$pages[] = $next;
				$paging['next'] = $next;
			}
			
			$paging['pages'] = $pages;
		}
		
		return $paging;
	}

	public function simple($baseUrl, $totalPages, $page)
	{
		if ($totalPages > 1) {
			$paging = [];
			$pages = [];
			
			if ($page > 1) {
				$prev = $this->page($baseUrl, $page - 1, false, $this->renderer->prev(), 'Предыдущая страница');
				$paging['prev'] = $prev;
				$pages[] = $prev;
			}

			for ($i = 1; $i <= $totalPages; $i++) {
				$pages[] = $this->page($baseUrl , $i, $i == $page);
			}
			
			if ($page < $totalPages) {
				$next = $this->page($baseUrl, $page + 1, false, $this->renderer->next(), 'Следующая страница');
				$paging['next'] = $next;
				$pages[] = $next;
			}
			
			$paging['page'] = $page;
			$paging['pages'] = $pages;

			return $paging;
		}
	}

	private function page($baseUrl, $page, $current, $label = null, $title = null)
	{
		return [
			'page' => $page,
			'current' => $current,
			'url' => $this->linker->page($baseUrl, $page),
			'label' => ($label != null) ? $label : $page,
			'title' => ($title != null) ? $title : "Страница {$page}",
		];
	}
}
