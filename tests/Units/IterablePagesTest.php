<?php

declare(strict_types=1);

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use ResponzivePagination\Paginator;

final class IterablePagesTest extends TestCase
{
	public function testIterationOverPagesObject(): void
	{
		$paginator = (new Paginator(5, 10, 3))->generate();

		$countIterations = 0;
		foreach ($paginator as $key => $value) {
			$this->assertEquals($paginator->buttons[$key], $value);
			$countIterations += 1;
		}

		$this->assertSame(\count($paginator->buttons), $countIterations);
	}
}
