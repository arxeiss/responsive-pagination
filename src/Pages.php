<?php

declare(strict_types=1);

namespace ResponzivePagination;

use Iterator;
use ResponzivePagination\PageItem;

class Pages implements Iterator
{
	/** @var PageItem */
	public $prev = null;

	/** @var PageItem */
	public $next = null;

	/** @var PageItem */
	public $current = null;

	/** @var PageItem[] */
	public $buttons = [];

	/** @var int Iterator position */
	private $position;

	public function __construct()
	{
		$this->position = 0;
	}

	public function rewind(): void
	{
		$this->position = 0;
	}

	public function current(): PageItem
	{
		return $this->buttons[$this->position];
	}

	public function key(): int
	{
		return $this->position;
	}

	public function next(): void
	{
		$this->position += 1;
	}

	public function valid(): bool
	{
		return isset($this->buttons[$this->position]);
	}
}
