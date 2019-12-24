<?php

use PHPUnit\Framework\TestCase;
use ResponzivePagination\Paginator;

final class PaginatorTest extends TestCase {

	public function testGenerate(): void
	{
		$paginator = (new Paginator(1, 0))->generate();

		$this->assertNull($paginator->prev);
		$this->assertNull($paginator->next);
		$this->assertNull($paginator->current);

		$this->assertIsArray($paginator->buttons);
		// $this->assertEmpty($paginator->buttons);
	}

	public function testGenerateEmpty(): void
	{
		$paginator = (new Paginator(1, 0))->generate();

		$this->assertNull($paginator->prev);
		$this->assertNull($paginator->next);
		// $this->assertNull($paginator->current);

		$this->assertIsArray($paginator->buttons);
		// $this->assertEmpty($paginator->buttons);
	}
}
