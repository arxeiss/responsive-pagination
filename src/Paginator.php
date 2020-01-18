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

	/** @var array<int, string[]> */
	protected $breakpoints = [];

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
	 * Add breakpoint when paginator buttons should be hidden and dots visible
	 *
	 * @param int    $maxVisible        Max visible buttons, should be odd number
	 * @param string $hiddenButtonClass CSS class to hide paginator button below
	 * @param string $visibleDotsClass  CSS class to show dots if needed
	 */
	public function addBreakpoint(int $maxVisible, string $hiddenButtonClass, string $visibleDotsClass): self
	{
		$this->breakpoints[$maxVisible] = [$hiddenButtonClass, $visibleDotsClass];

		return $this;
	}

	/**
	 * Generate final pagination
	 */
	public function generate(): Pages
	{
		$paginator = $this->generatePages();

		\krsort($this->breakpoints);
		foreach ($this->breakpoints as $maxVisible => [$hiddenButtonClass, $visibleDotsClass]) {
			$paginator = $this->applyBreakpointClasses($paginator, $maxVisible, $hiddenButtonClass, $visibleDotsClass);
		}

		return $paginator;
	}

	/**
	 * Create element in final pagination
	 *
	 * @param int $page        Into which page this elements is linking
	 * @param int $currentPage Currently opened page
	 */
	protected function createElement(int $page, int $currentPage = 0): PageItem
	{
		return new PageItem(
			$page,
			$page === $currentPage,
			false,
			null,
			\str_replace('%%page%%', (string)$page, $page === 1 ? $this->firstPageLinkFormat : $this->pageLinkFormat)
		);
	}

	/**
	 * Create element which is only dots
	 */
	protected function createDots(?string $addClass = null): PageItem
	{
		return new PageItem(
			null,
			false,
			true,
			$addClass,
			null
		);
	}

	/**
	 * Generate final Pages object
	 */
	protected function generatePages(): Pages
	{
		$paginator = new Pages();
		$range = $this->range;

		// If there is only 1 page
		if ($this->totalPages <= 1) {
			$paginator->buttons[] = $paginator->current = $this->createElement(1, 1);

			return $paginator;
		}

		$paginator->current = $this->createElement($this->currentPage, $this->currentPage);

		// If is first/last (or second/second from the end) page increase range,
		// as range cannot be applied to both directions equally
		if ($this->currentPage === 1 || $this->currentPage === $this->totalPages) {
			$range += 2;
		} elseif ($this->currentPage === 2 || $this->currentPage === $this->totalPages - 1) {
			$range += 1;
		}

		// Where the middle part (exclude first and last page) starts
		$startMiddle = \max($this->currentPage - $range, 2);
		$endMiddle = \min($this->currentPage + $range, $this->totalPages - 1);

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

		for ($i = $startMiddle; $i <= $endMiddle; $i += 1) {
			$paginator->buttons[] = $this->createElement($i, $this->currentPage);
		}

		if ($this->currentPage !== $this->totalPages) {
			$paginator->next = $this->createElement($this->currentPage + 1);

			// If middle part ends at 3rd page from the end, show always 2nd last page instead of dots
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

		$toHide = \count($buttons) - $maxVisible;
		if ($toHide < 1) {
			return $paginator;
		}

		// Search index of active element
		$middleIndex = self::arrayCallbackSearch($buttons, static function ($button) {
			return $button->active === true;
		});
		// Search for index of the left dots element - there will be max 1 dots element on the left from middle index
		$leftDotsIndex = self::arrayCallbackSearch($buttons, static function ($button, $key) use ($middleIndex) {
			return $button->dots && $key < $middleIndex;
		});
		// Search for index of the right dots element - there will be max 1 dots element on the right from middle index
		$rightDotsIndex = self::arrayCallbackSearch($buttons, static function ($button, $key) use ($middleIndex) {
			return $button->dots && $key > $middleIndex;
		});

		// There are no dots on the left side but breakpoint should hide some of the elements on the left side
		if (!$leftDotsIndex && $this->currentPage > (int)\ceil($maxVisible / 2)) {
			$toHide += 1;

			// Insert new dots element to index 1
			\array_splice($buttons, 1, 0, [$this->createDots($visibleDotsClass)]);
			$leftDotsIndex = 1;

			// Move also other pointers when dots button on left side is added
			$middleIndex += 1;
			if ($rightDotsIndex) {
				$rightDotsIndex += 1;
			}
		}
		// There are no dots on the right side, but breakpoint should hide some of elements on the right side
		if (!$rightDotsIndex && $this->currentPage < $this->totalPages - (int)\floor($maxVisible / 2)) {
			$toHide += 1;
			\array_splice($buttons, -1, 0, [$this->createDots($visibleDotsClass)]);
			$rightDotsIndex = \count($buttons) - 2; // -1 because of index and -1 to 1 position before end
		}

		$hideFromLeft = 0;
		$hideFromRight = 0;

		// How many buttons on the right side and left side should have hiding class and add this class
		if (!$leftDotsIndex && $rightDotsIndex) {
			$hideFromRight = $toHide;
		} elseif ($leftDotsIndex && !$rightDotsIndex) {
			$hideFromLeft = $toHide;
		} elseif ($leftDotsIndex && $rightDotsIndex) {
			// How many buttons around actual index should be visible
			$middleVisibleOffset = (int)\ceil((\count($buttons) - $toHide - 5) / 2);
			$hideFromLeft = $middleIndex - 2 - $middleVisibleOffset;
			$hideFromRight = \count($buttons) - $middleIndex - 3 - $middleVisibleOffset;
		}

		for ($i = 0; $i < $hideFromLeft; $i += 1) {
			$buttonIndex = $leftDotsIndex + $i + 1;
			if (!$buttons[$buttonIndex]->className) {
				$buttons[$buttonIndex]->className = $hiddenButtonClass;
			}
		}

		for ($i = 0; $i < $hideFromRight; $i += 1) {
			$buttonIndex = $rightDotsIndex - $i - 1;
			if (!$buttons[$buttonIndex]->className) {
				$buttons[$buttonIndex]->className = $hiddenButtonClass;
			}
		}

		return $paginator;
	}

	/**
	 * Search index in array by condition in callback
	 *
	 * @param  mixed[] $arr
	 * @return mixed
	 */
	private static function arrayCallbackSearch(array $arr, callable $searchCallback)
	{
		foreach ($arr as $key => $item) {
			if (\call_user_func($searchCallback, $item, $key)) {
				return $key;
			}
		}
	}
}
