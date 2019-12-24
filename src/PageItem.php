<?php

declare(strict_types=1);

namespace ResponzivePagination;

class PageItem
{
	/** @var int */
	public $page;

	/** @var bool */
	public $active;

	/** @var bool */
	public $dots;

	/** @var ?string */
	public $className;

	/** @var string */
	public $link;

	/**
	 * @param int $page
	 * @param bool $active
	 * @param bool $dots
	 * @param string $className
	 * @param string $link
	 */
	public function __construct(
		int $page,
		bool $active,
		bool $dots,
		?string $className,
		string $link
	) {
		$this->page = $page;
		$this->active = $active;
		$this->dots = $dots;
		$this->className = $className;
		$this->link = $link;
	}
}
