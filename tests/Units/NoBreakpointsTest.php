<?php

declare(strict_types=1);

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use ResponzivePagination\PageItem;
use ResponzivePagination\Paginator;

final class NoBreakpointsTest extends TestCase
{
	/**
	 * @return array<string, int>
	 */
	public function generateEmptyDataProvider(): array
	{
		return [
			'Page 0 of 0' => [0, 0],
			'Page 0 of 1' => [0, 1],
			'Page 1 of 0' => [1, 0],
			'Page 1 of 1' => [1,1],
		];
	}

	/**
	 * @dataProvider generateEmptyDataProvider
	 */
	public function testGenerateEmpty(int $page, int $pages): void
	{
		$paginator = (new Paginator($page, $pages))->generate();

		$this->assertNull($paginator->prev);
		$this->assertNull($paginator->next);
		$this->assertInstanceOf(PageItem::class, $paginator->current);
		$this->assertSame(1, $paginator->current->page);
		$this->assertTrue($paginator->current->active);

		$this->assertIsArray($paginator->buttons);
		$this->assertCount(1, $paginator->buttons);
		$this->assertEquals($paginator->buttons[0], $paginator->current);
	}

	/**
	 * @return array<string, int>
	 */
	public function generateDataProvider(): array
	{
		$range4Pages10 = [
			'Page 1 of 10 (range = 4)' 	=> [1, 10, 4, 0, \array_merge(\range(1, 7), ['...', 10])],
			'Page 2 of 10 (range = 4)' 	=> [2, 10, 4, 1, \array_merge(\range(1, 7), ['...', 10])],
			'Page 3 of 10 (range = 4)' 	=> [3, 10, 4, 2, \array_merge(\range(1, 7), ['...', 10])],
			'Page 4 of 10 (range = 4)' 	=> [4, 10, 4, 3, \range(1, 10)],
			'Page 5 of 10 (range = 4)' 	=> [5, 10, 4, 4, \range(1, 10)],
			'Page 6 of 10 (range = 4)' 	=> [6, 10, 4, 5, \range(1, 10)],
			'Page 7 of 10 (range = 4)' 	=> [7, 10, 4, 6, \range(1, 10)],
			'Page 8 of 10 (range = 4)' 	=> [8, 10, 4, 6, \array_merge([1, '...'], \range(4, 10))],
			'Page 9 of 10 (range = 4)' 	=> [9, 10, 4, 7, \array_merge([1, '...'], \range(4, 10))],
			'Page 10 of 10 (range = 4)' => [10, 10, 4, 8, \array_merge([1, '...'], \range(4, 10))],
		];

		$range5Pages30 = [
			'Page 1 of 30 (range = 5)' => [1, 30, 5, 0, \array_merge(\range(1, 8), ['...', 30])],
			'Page 2 of 30 (range = 5)' => [2, 30, 5, 1, \array_merge(\range(1, 8), ['...', 30])],
			'Page 3 of 30 (range = 5)' => [3, 30, 5, 2, \array_merge(\range(1, 8), ['...', 30])],
			'Page 4 of 30 (range = 5)' => [4, 30, 5, 3, \array_merge(\range(1, 9), ['...', 30])],
			'Page 5 of 30 (range = 5)' => [5, 30, 5, 4, \array_merge(\range(1, 10), ['...', 30])],
			'Page 6 of 30 (range = 5)' => [6, 30, 5, 5, \array_merge(\range(1, 11), ['...', 30])],
			'Page 7 of 30 (range = 5)' => [7, 30, 5, 6, \array_merge(\range(1, 12), ['...', 30])],
			'Page 8 of 30 (range = 5)' => [8, 30, 5, 7, \array_merge(\range(1, 13), ['...', 30])],
		];

		for ($i = 9; $i < 23; $i += 1) {
			$range5Pages30["Page {$i} of 30 (range = 5)"] = [$i, 30, 5, 7, \array_merge(
				[1, '...'],
				\range($i - 5, $i + 5),
				['...', 30]
			)];
		}

		$range5Pages30['Page 23 of 30 (range = 5)'] = [23, 30, 5, 7, \array_merge([1, '...'], \range(18, 30))];
		$range5Pages30['Page 24 of 30 (range = 5)'] = [24, 30, 5, 7, \array_merge([1, '...'], \range(19, 30))];
		$range5Pages30['Page 25 of 30 (range = 5)'] = [25, 30, 5, 7, \array_merge([1, '...'], \range(20, 30))];
		$range5Pages30['Page 26 of 30 (range = 5)'] = [26, 30, 5, 7, \array_merge([1, '...'], \range(21, 30))];
		$range5Pages30['Page 27 of 30 (range = 5)'] = [27, 30, 5, 7, \array_merge([1, '...'], \range(22, 30))];
		$range5Pages30['Page 28 of 30 (range = 5)'] = [28, 30, 5, 7, \array_merge([1, '...'], \range(23, 30))];
		$range5Pages30['Page 29 of 30 (range = 5)'] = [29, 30, 5, 8, \array_merge([1, '...'], \range(23, 30))];
		$range5Pages30['Page 30 of 30 (range = 5)'] = [30, 30, 5, 9, \array_merge([1, '...'], \range(23, 30))];

		return \array_merge($range4Pages10, $range5Pages30);
	}

	/**
	 * @param mixed[] $buttons
	 *
	 * @dataProvider generateDataProvider
	 */
	public function testGenerate(int $page, int $pages, int $range, int $activeIndex, array $buttons): void
	{
		$paginator = (new Paginator($page, $pages, $range))->generate();

		if ($page === 1) {
			$this->assertNull($paginator->prev);
		} else {
			$this->assertSame($page - 1, $paginator->prev->page);
		}
		if ($page === $pages) {
			$this->assertNull($paginator->next);
		} else {
			$this->assertSame($page + 1, $paginator->next->page);
		}

		$this->assertSame($page, $paginator->current->page);
		$this->assertIsArray($paginator->buttons);
		$this->assertCount(\count($buttons), $paginator->buttons);
		$this->assertEquals($paginator->buttons[$activeIndex], $paginator->current);

		foreach ($buttons as $index => $button) {
			if ($button === '...') {
				$this->assertTrue($paginator->buttons[$index]->dots, "There are no dots on index {$index}.");

				continue;
			}

			$this->assertSame($button, $paginator->buttons[$index]->page, "On index {$index} is not number {$button}.");
		}
	}
}
