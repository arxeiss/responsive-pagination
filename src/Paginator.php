<?php

declare(strict_types=1);

namespace ResponsivePagination;

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
		if ($currentPage < 0) {
			throw new \ResponsivePagination\Exceptions\InvalidArgumentException(
				'Parameter $currentPage must be greater or equals to 0'
			);
		}
		if ($totalPages < 0) {
			throw new \ResponsivePagination\Exceptions\InvalidArgumentException(
				'Parameter $totalPages must be greater or equals to 0'
			);
		}
		if ($range < 0) {
			throw new \ResponsivePagination\Exceptions\InvalidArgumentException(
				'Parameter $range must be greater or equals to 0'
			);
		}
		$this->currentPage = $currentPage;
		$this->totalPages = $totalPages;
		$this->range = $range;
		$this->pageLinkFormat = $pageLinkFormat;
		$this->firstPageLinkFormat = $firstPageLinkFormat ?? $pageLinkFormat;
	}

	/**
	 * Add breakpoint when paginator buttons should be hidden and dots visible
	 *
	 * @param int    $range             Max visible buttons, should be odd number
	 * @param string $hiddenButtonClass CSS class to hide paginator button below
	 * @param string $visibleDotsClass  CSS class to show dots if needed
	 */
	public function addBreakpoint(int $range, string $hiddenButtonClass, string $visibleDotsClass): self
	{
		if ($range < 0) {
			throw new \ResponsivePagination\Exceptions\InvalidArgumentException(
				'Parameter $range must be greater or equals to 0'
			);
		}
		$this->breakpoints[$range] = [$hiddenButtonClass, $visibleDotsClass];

		return $this;
	}

	/**
	 * Generate final pagination
	 */
	public function generate(): Pages
	{
		$paginator = $this->generatePages();

		\krsort($this->breakpoints);
		foreach ($this->breakpoints as $range => [$hiddenButtonClass, $visibleDotsClass]) {
			$paginator = $this->applyBreakpointClasses($paginator, $range, $hiddenButtonClass, $visibleDotsClass);
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
			$this->formatLink($page)
		);
	}

	/**
	 * Format URL based on given page and link formats
	 */
	public function formatLink(int $page): string
	{
		return \str_replace(
			'%%page%%',
			(string)$page,
			$page === 1 ? $this->firstPageLinkFormat : $this->pageLinkFormat
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

		// If there is only 1 page
		if ($this->totalPages <= 1) {
			$paginator->buttons[] = $paginator->current = $this->createElement(1, 1);

			return $paginator;
		}

		$paginator->current = $this->createElement($this->currentPage, $this->currentPage);

		$range = $this->getRange();
		// Where the middle part (exclude first and last page) starts
		$startMiddle = \max($this->currentPage - $range, 2);
		$endMiddle = \min($this->currentPage + $range, $this->totalPages - 1);

		$paginator->buttons[] = $this->createElement(1, $this->currentPage);
		$this->addBeginningButtons($paginator, $startMiddle);

		for ($i = $startMiddle; $i <= $endMiddle; $i += 1) {
			$paginator->buttons[] = $this->createElement($i, $this->currentPage);
		}

		$this->addEndingButtons($paginator, $endMiddle);
		$paginator->buttons[] = $this->createElement($this->totalPages, $this->currentPage);

		return $paginator;
	}

	protected function getRange(): int
	{
		$range = $this->range;
		// If is first/last (or second/second from the end) page increase range,
		// as range cannot be applied to both directions equally
		if ($this->currentPage === 1 || $this->currentPage === $this->totalPages) {
			$range += 2;
		} elseif ($this->currentPage === 2 || $this->currentPage === $this->totalPages - 1) {
			$range += 1;
		}

		return $range;
	}

	protected function addBeginningButtons(Pages $paginator, int $startMiddle): void
	{
		if ($this->currentPage !== 1) {
			$paginator->prev = $this->createElement($this->currentPage - 1);

			// If middle part starts at 3rd page, show always 2nd page instead of dots
			if ($startMiddle === 3) {
				$paginator->buttons[] = $this->createElement(2, $this->currentPage);
			} elseif ($startMiddle > 3) {
				$paginator->buttons[] = $this->createDots();
			}
		}
	}

	protected function addEndingButtons(Pages $paginator, int $endMiddle): void
	{
		if ($this->currentPage !== $this->totalPages) {
			$paginator->next = $this->createElement($this->currentPage + 1);

			// If middle part ends at 3rd page from the end, show always 2nd last page instead of dots
			if ($endMiddle === $this->totalPages - 2) {
				$paginator->buttons[] = $this->createElement($this->totalPages - 1, $this->currentPage);
			} elseif ($endMiddle < $this->totalPages - 2) {
				$paginator->buttons[] = $this->createDots();
			}
		}
	}

	protected function applyBreakpointClasses(
		Pages $paginator,
		int $range,
		string $hiddenButtonClass,
		string $visibleDotsClass
	): Pages {
		$maxVisible = static::rangeToMaxVisible($range);
		$toHide = \count($paginator->buttons) - $maxVisible;
		if ($toHide < 1) {
			return $paginator;
		}

		[$leftDotsIndex, $middleIndex, $rightDotsIndex] = $this->getDotsIndex($paginator);

		// There are no dots on the left side but breakpoint should hide some of the elements on the left side
		if (!$leftDotsIndex && $this->currentPage > (int)\ceil($maxVisible / 2)) {
			$toHide += 1;

			// Insert new dots element to index 1
			\array_splice($paginator->buttons, 1, 0, [$this->createDots($visibleDotsClass)]);
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
			\array_splice($paginator->buttons, -1, 0, [$this->createDots($visibleDotsClass)]);
			$rightDotsIndex = \count($paginator->buttons) - 2; // -1 because of index and -1 to 1 position before end
		}

		$this->addButtonClasses($paginator, $middleIndex, $leftDotsIndex, $rightDotsIndex, $toHide, $hiddenButtonClass);

		return $paginator;
	}

	private function addButtonClasses(
		Pages $paginator,
		int $middleIndex,
		?int $leftDotsIndex,
		?int $rightDotsIndex,
		int $toHide,
		string $hiddenButtonClass
	): void {
		$hideFromLeft = 0;
		$hideFromRight = 0;

		// How many buttons on the right side and left side should have hiding class and add this class
		if (!$leftDotsIndex && $rightDotsIndex) {
			$hideFromRight = $toHide;
		} elseif ($leftDotsIndex && !$rightDotsIndex) {
			$hideFromLeft = $toHide;
		} elseif ($leftDotsIndex && $rightDotsIndex) {
			// How many buttons around actual index should be visible
			$middleVisibleOffset = (int)\ceil((\count($paginator->buttons) - $toHide - 5) / 2);
			$hideFromLeft = $middleIndex - 2 - $middleVisibleOffset;
			$hideFromRight = \count($paginator->buttons) - $middleIndex - 3 - $middleVisibleOffset;
		}

		for ($i = 0; $i < $hideFromLeft; $i += 1) {
			$buttonIndex = $leftDotsIndex + $i + 1;
			if (!$paginator->buttons[$buttonIndex]->className) {
				$paginator->buttons[$buttonIndex]->className = $hiddenButtonClass;
			}
		}

		for ($i = 0; $i < $hideFromRight; $i += 1) {
			$buttonIndex = $rightDotsIndex - $i - 1;
			if (!$paginator->buttons[$buttonIndex]->className) {
				$paginator->buttons[$buttonIndex]->className = $hiddenButtonClass;
			}
		}
	}

	/**
	 * @return array<int>
	 */
	protected function getDotsIndex(Pages $paginator): array
	{
		// Search index of active element
		$middleIndex = self::arrayCallbackSearch($paginator->buttons, static function ($button) {
			return $button->active === true;
		});
		// Search for index of the left dots element - there will be max 1 dots element on the left from middle index
		$leftDotsIndex = self::arrayCallbackSearch(
			$paginator->buttons,
			static function ($button, $key) use ($middleIndex) {
				return $button->dots && $key < $middleIndex;
			}
		);
		// Search for index of the right dots element - there will be max 1 dots element on the right from middle index
		$rightDotsIndex = self::arrayCallbackSearch(
			$paginator->buttons,
			static function ($button, $key) use ($middleIndex) {
				return $button->dots && $key > $middleIndex;
			}
		);

		return [$leftDotsIndex, $middleIndex, $rightDotsIndex];
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

	/**
	 * Convert given range into maximum amount of visible buttons including dots and excluding prev and next button
	 */
	public static function rangeToMaxVisible(int $range): int
	{
		if ($range < 0) {
			throw new \ResponsivePagination\Exceptions\InvalidArgumentException(
				'Parameter $range must be greater or equals to 0'
			);
		}

		// First and last button + first and last dots + middle button + 2x range around middle button
		return $range * 2 + 5;
	}
}
