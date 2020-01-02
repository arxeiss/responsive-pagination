<?php

declare(strict_types=1);

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use ResponzivePagination\Paginator;

final class BreakpointsTest extends TestCase
{
	const B11_BUTTON = 'd-none d-lg-block';
	const B11_DOTS = 'd-lg-none';
	const B9_BUTTON = 'd-none d-md-block';
	const B9_DOTS = 'd-md-none';
	const B7_BUTTON = 'd-none d-sm-block';
	const B7_DOTS = 'd-sm-none';

	/**
	 * @return array<string, int>
	 */
	public function generateDataProvider(): array
	{
		$range4Pages20 = [
			'Page 1 of 20 (range = 4)' => [1, [5 => self::B7_BUTTON, 6 => self::B7_BUTTON]],
			'Page 2 of 20 (range = 4)' => [2, [5 => self::B7_BUTTON, 6 => self::B7_BUTTON]],
			'Page 3 of 20 (range = 4)' => [3, [5 => self::B7_BUTTON, 6 => self::B7_BUTTON]],
			'Page 4 of 20 (range = 4)' => [4, [5 => self::B7_BUTTON, 6 => self::B7_BUTTON, 7 => self::B9_BUTTON]],
			'Page 5 of 20 (range = 4)' => [
				5,
				[
					1 => self::B7_DOTS,
					2 => self::B7_BUTTON,
					3 => self::B7_BUTTON,
					7 => self::B7_BUTTON,
					8 => self::B9_BUTTON,
					9 => self::B9_BUTTON,
				],
			],
			'Page 6 of 20 (range = 4)' => [
				6,
				[
					1 => self::B9_DOTS,
					2 => self::B9_BUTTON,
					3 => self::B9_BUTTON,
					4 => self::B7_BUTTON,
					8 => self::B7_BUTTON,
					9 => self::B9_BUTTON,
					10 => self::B11_BUTTON,
				],
			],
			'Page 7 of 20 (range = 4)' => [
				7,
				[
					1 => self::B11_DOTS,
					2 => self::B11_BUTTON,
					3 => self::B11_BUTTON,
					4 => self::B9_BUTTON,
					5 => self::B7_BUTTON,
					9 => self::B7_BUTTON,
					10 => self::B9_BUTTON,
					11 => self::B11_BUTTON,
				],
			],
		];

		for ($i = 8; $i < 14; $i += 1) {
			$index = \sprintf('Page %d of 20 (range = 4)', $i);
			$range4Pages20[$index] = [
				$i,
				[
					2 => self::B11_BUTTON,
					3 => self::B9_BUTTON,
					4 => self::B7_BUTTON,
					8 => self::B7_BUTTON,
					9 => self::B9_BUTTON,
					10 => self::B11_BUTTON,
				],
			];
		}

		$range4Pages20['Page 14 of 20 (range = 4)'] = [
			14,
			[
				2 => self::B11_BUTTON,
				3 => self::B9_BUTTON,
				4 => self::B7_BUTTON,
				8 => self::B7_BUTTON,
				9 => self::B9_BUTTON,
				10 => self::B11_BUTTON,
				11 => self::B11_BUTTON,
				12 => self::B11_DOTS,
			],
		];
		$range4Pages20['Page 15 of 20 (range = 4)'] = [
			15,
			[
				2 => self::B11_BUTTON,
				3 => self::B9_BUTTON,
				4 => self::B7_BUTTON,
				8 => self::B7_BUTTON,
				9 => self::B9_BUTTON,
				10 => self::B9_BUTTON,
				11 => self::B9_DOTS,
			],
		];
		$range4Pages20['Page 16 of 20 (range = 4)'] = [
			16,
			[
				2 => self::B9_BUTTON,
				3 => self::B9_BUTTON,
				4 => self::B7_BUTTON,
				8 => self::B7_BUTTON,
				9 => self::B7_BUTTON,
				10 => self::B7_DOTS,
			],
		];
		$range4Pages20['Page 17 of 20 (range = 4)'] = [
			17,
			[2 => self::B9_BUTTON, 3 => self::B7_BUTTON, 4 => self::B7_BUTTON],
		];
		$range4Pages20['Page 18 of 20 (range = 4)'] = [18, [3 => self::B7_BUTTON, 2 => self::B7_BUTTON]];
		$range4Pages20['Page 19 of 20 (range = 4)'] = [19, [3 => self::B7_BUTTON, 2 => self::B7_BUTTON]];
		$range4Pages20['Page 20 of 20 (range = 4)'] = [20, [3 => self::B7_BUTTON, 2 => self::B7_BUTTON]];

		return $range4Pages20;
	}

	/**
	 * @param        mixed[] $buttonClasses
	 * @dataProvider generateDataProvider
	 */
	public function testGenerate(int $page, array $buttonClasses): void
	{
		$paginator = (new Paginator($page, 20))
			->addBreakpoint(11, self::B11_BUTTON, self::B11_DOTS)
			->addBreakpoint(9, self::B9_BUTTON, self::B9_DOTS)
			->addBreakpoint(7, self::B7_BUTTON, self::B7_DOTS)
			->generate();

		foreach ($paginator as $index => $button) {
			if (isset($buttonClasses[$index])) {
				$this->assertSame(
					$buttonClasses[$index],
					$button->className,
					\sprintf('There is wrong class on button index %d', $index)
				);

				continue;
			}

			$this->assertEmpty($button->className, \sprintf('There should be no classes on button index %d', $index));
		}
	}
}
