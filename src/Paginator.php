<?php

declare(strict_types=1);

namespace ResponzivePagination;

class Paginator
{
	/** @var int */
	protected $currentPage;

	/** @var int */
	protected $totalPages;

	/** @var int */
	protected $range;

	/** @var string */
	protected $pageLinkFormat;

	/** @var string */
	protected $firstPageLinkFormat;

	/** @var array */
	protected $breakpoints = [];

	/**
	 * @param int    $currentPage
	 * @param int $totalPages
	 * @param int $range
	 * @param string $pageLinkFormat
	 * @param string $firstPageLinkFormat
	 */
	public function __construct(
		int $currentPage,
		int $totalPages,
		int $range = 4,
		string $pageLinkFormat = '?page=%%page%%',
		?string $firstPageLinkFormat = null
	) {
		$this->currentPage = $currentPage;
		$this->totalPages = $totalPages;
		$this->range = $range;
		$this->pageLinkFormat = $pageLinkFormat;
		$this->firstPageLinkFormat = $firstPageLinkFormat ?? $pageLinkFormat;
	}

	/**
	 * Add breakpoint when paginator buttons are hidden or visible
	 *
	 * @param int $maxVisible Max visible buttons
	 * @param string $hiddenButtonClass CSS class to hide paginator button
	 * @param string $visibleDotsClass CSS class to show dots if needed
	 */
	public function addBreakpoint(int $maxVisible, string $hiddenButtonClass, string $visibleDotsClass): self
	{
		$this->breakpoints[$maxVisible] = [$hiddenButtonClass, $visibleDotsClass];

		return $this;
	}

	public function generate(): Pages
	{
		$paginator = $this->generatePages();

			//      abc
		krsort($this->breakpoints);
		foreach ($this->breakpoints as $maxVisible => [$hiddenButtonClass, $visibleDotsClass]) {
			$paginator = $this->applyBreakpointClasses($paginator, $maxVisible, $hiddenButtonClass, $visibleDotsClass);
		}

		return $paginator;
	}

	/**
	 * @param int $page
	 * @param int $currentPage
	 * @param bool $dots
	 * @param string $className
	 * @return PageItem
	 */
	protected function createElement(
		int $page,
		int $currentPage = 0,
		bool $dots = false,
		?string $className = null
	): PageItem {
		return new PageItem(
			$page,
			$page === $currentPage && !$dots,
			$dots,
			$className,
			str_replace('"%%page%%', $page, $page === 1 ? $this->firstPageLinkFormat : $this->pageLinkFormat)
		);
	}

	/**
	 * @param string|null $addClass
	 * @return PageItem
	 */
	protected function createDots(?string $addClass = null): PageItem
	{
		return $this->createElement(0, 0, true, $addClass);
	}

	/**
	 * Generate main paginator Pages
	 *
	 * @return Pages
	 */
	protected function generatePages(): Pages
	{
		$paginator = new Pages();
		$paginator->current = $this->createElement($this->currentPage, $this->currentPage);
		$range = $this->range;

		// If there is only 1 page
		if ($this->totalPages <= 1) {
			$paginator->buttons[] = $this->createElement(1, 1);

			return $paginator;
		}

		// If is first/last (or second/second from the end) page increase range,
		// as range cannot be applied to both directions equally
		if ($this->currentPage === 1 || $this->currentPage === $this->totalPages) {
			$range += 2;
		} elseif ($this->currentPage === 2 || $this->currentPage === $this->totalPages - 1) {
			$range += 1;
		}

		// Where the middle part (exclude first and last page) starts
		$startMiddle = max($this->currentPage - $range, 2);
		$endMiddle = min($this->currentPage + $range, $this->totalPages - 1);

		$paginator->buttons[] = $this->createElement(1, $this->currentPage);

		if ($this->currentPage !== 1) {
			$paginator->prev = $this->createElement($this->currentPage - 1);

			// If middle part starts at 3rd page, show always 2nd page instead of dots
			if ($startMiddle === 3) {
				$paginator->buttons[] = $this->createElement(2, $this->currentPage);
			} elseif ($startMiddle > 3) {
				$paginator->buttons[] = $this->createDots();
			}
		}

		for ($i = $startMiddle; $i <= $endMiddle; $i++) {
			$paginator->buttons[] = $this->createElement($i, $this->currentPage);
		}

		if ($this->currentPage !== $this->totalPages) {
			$paginator->next = $this->createElement($this->currentPage + 1);

			// Same as above
			if ($endMiddle === $this->totalPages - 2) {
				$paginator->buttons[] = $this->createElement($this->totalPages - 1, $this->currentPage);
			} elseif ($endMiddle < $this->totalPages - 2) {
				$paginator->buttons[] = $this->createDots();
			}
		}

		$paginator->buttons[] = $this->createElement($this->totalPages, $this->currentPage);

		return $paginator;
	}

	protected function applyBreakpointClasses(
		Pages $paginator,
		int $maxVisible,
		string $hiddenButtonClass,
		string $visibleDotsClass
	): Pages {
		$buttons = &$paginator->buttons;

		$toHide = count($buttons) - $maxVisible;
		if ($toHide < 1) {
			return $paginator;
		}

		$middleIndex = self::arrayCallbackSearch($buttons, function ($button) {
			return $button->active === true;
		});
		$leftDotsIndex = self::arrayCallbackSearch($buttons, function ($button, $key) use ($middleIndex) {
			return $button->dots && $key < $middleIndex;
		});
		$rightDotsIndex = self::arrayCallbackSearch($buttons, function ($button, $key) use ($middleIndex) {
			return $button->dots && $key > $middleIndex;
		});

		if (!$leftDotsIndex && $this->currentPage > (int)ceil($maxVisible / 2)) {
			$toHide += 1;
			array_splice($buttons, 1, 0, [$this->createDots($visibleDotsClass)]);
			$leftDotsIndex = 1;

			// Move also other pointers when dots button on left side is added
			$middleIndex += 1;
			if ($rightDotsIndex) {
				$rightDotsIndex += 1;
			}
		}
		if (!$rightDotsIndex && $this->currentPage < $this->totalPages - (int)floor($maxVisible / 2)) {
			$toHide += 1;
			array_splice($buttons, -1, 0, [$this->createDots($visibleDotsClass)]);
			$rightDotsIndex = count($buttons) - 2; // -1 because of index and -1 to 1 position before end
		}

		$hideFromLeft = 0;
		$hideFromRight = 0;

		if (!$leftDotsIndex && $rightDotsIndex) {
			$hideFromRight = $toHide;
		} elseif ($leftDotsIndex && !$rightDotsIndex) {
			$hideFromLeft = $toHide;
		} elseif ($leftDotsIndex && $rightDotsIndex) {
			// How many buttons around actual index should be visible
			$middleVisibleOffset = (int)ceil((count($buttons) - $toHide - 5) / 2);
			$hideFromLeft = $middleIndex - 2 - $middleVisibleOffset;
			$hideFromRight = count($buttons) - $middleIndex - 3 - $middleVisibleOffset;
		}

		for ($i = 0; $i < $hideFromLeft; $i++) {
			$buttonIndex = $leftDotsIndex + $i + 1;
			if (! $buttons[$buttonIndex]->className) {
				$buttons[$buttonIndex]->className = $hiddenButtonClass;
			}
		}

		for ($i = 0; $i < $hideFromRight; $i++) {
			$buttonIndex = $rightDotsIndex - $i - 1;
			if (! $buttons[$buttonIndex]->className) {
				$buttons[$buttonIndex]->className = $hiddenButtonClass;
			}
		}

		return $paginator;
	}

	/**
	 * Search index in array by condition in callback
	 *
	 * @param array $arr
	 * @param callable $searchCallback
	 * @return mixed
	 */
	private static function arrayCallbackSearch(array $arr, callable $searchCallback)
	{
		foreach ($arr as $key => $item) {
			if (call_user_func($searchCallback, $item, $key)) {
				return $key;
			}
		}
	}
}
