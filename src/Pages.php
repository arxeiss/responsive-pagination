<?php

declare(strict_types=1);

namespace ResponzivePagination;

class Pages
{
	/** @var PageItem */
	public $prev = null;

	/** @var PageItem */
	public $next = null;

	/** @var PageItem */
	public $current = null;

	/** @var PageItem[] */
	public $buttons = [];
}
