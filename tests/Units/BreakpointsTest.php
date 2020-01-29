<?php

declare(strict_types=1);

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use ResponzivePagination\Paginator;

final class BreakpointsTest extends TestCase
{
	private const R3_BUTTON = 'd-none d-lg-block';
	private const R3_DOTS = 'd-lg-none';
	private const R2_BUTTON = 'd-none d-md-block';
	private const R2_DOTS = 'd-md-none';
	private const R0_BUTTON = 'd-none d-sm-block';
	private const R0_DOTS = 'd-sm-none';

	/**
	 * @return array<string, int>
	 */
	public function generateDataProvider(): array
	{
		$range4Pages20 = [
			'Page 1 of 20 (range = 4)' => [
				1,
				[
					3 => self::R0_BUTTON,
					4 => self::R0_BUTTON,
					5 => self::R0_BUTTON,
					6 => self::R0_BUTTON,
				],
			],
			'Page 2 of 20 (range = 4)' => [
				2,
				[
					3 => self::R0_BUTTON,
					4 => self::R0_BUTTON,
					5 => self::R0_BUTTON,
					6 => self::R0_BUTTON,
				],
			],
			'Page 3 of 20 (range = 4)' => [
				3,
				[
					3 => self::R0_BUTTON,
					4 => self::R0_BUTTON,
					5 => self::R0_BUTTON,
					6 => self::R0_BUTTON,
				],
			],
			'Page 4 of 20 (range = 4)' => [
				4,
				[
					1 => self::R0_DOTS,
					2 => self::R0_BUTTON,
					3 => self::R0_BUTTON,
					5 => self::R0_BUTTON,
					6 => self::R0_BUTTON,
					7 => self::R0_BUTTON,
					8 => self::R2_BUTTON,
				],
			],
			'Page 5 of 20 (range = 4)' => [
				5,
				[
					1 => self::R0_DOTS,
					2 => self::R0_BUTTON,
					3 => self::R0_BUTTON,
					4 => self::R0_BUTTON,
					6 => self::R0_BUTTON,
					7 => self::R0_BUTTON,
					8 => self::R2_BUTTON,
					9 => self::R2_BUTTON,
				],
			],
			'Page 6 of 20 (range = 4)' => [
				6,
				[
					1 => self::R2_DOTS,
					2 => self::R2_BUTTON,
					3 => self::R2_BUTTON,
					4 => self::R0_BUTTON,
					5 => self::R0_BUTTON,
					7 => self::R0_BUTTON,
					8 => self::R0_BUTTON,
					9 => self::R2_BUTTON,
					10 => self::R3_BUTTON,
				],
			],
			'Page 7 of 20 (range = 4)' => [
				7,
				[
					1 => self::R3_DOTS,
					2 => self::R3_BUTTON,
					3 => self::R3_BUTTON,
					4 => self::R2_BUTTON,
					5 => self::R0_BUTTON,
					6 => self::R0_BUTTON,
					8 => self::R0_BUTTON,
					9 => self::R0_BUTTON,
					10 => self::R2_BUTTON,
					11 => self::R3_BUTTON,
				],
			],
		];

		for ($i = 8; $i < 14; $i += 1) {
			$index = \sprintf('Page %d of 20 (range = 4)', $i);
			$range4Pages20[$index] = [
				$i,
				[
					2 => self::R3_BUTTON,
					3 => self::R2_BUTTON,
					4 => self::R0_BUTTON,
					5 => self::R0_BUTTON,
					7 => self::R0_BUTTON,
					8 => self::R0_BUTTON,
					9 => self::R2_BUTTON,
					10 => self::R3_BUTTON,
				],
			];
		}

		$range4Pages20['Page 14 of 20 (range = 4)'] = [
			14,
			[
				2 => self::R3_BUTTON,
				3 => self::R2_BUTTON,
				4 => self::R0_BUTTON,
				5 => self::R0_BUTTON,
				7 => self::R0_BUTTON,
				8 => self::R0_BUTTON,
				9 => self::R2_BUTTON,
				10 => self::R3_BUTTON,
				11 => self::R3_BUTTON,
				12 => self::R3_DOTS,
			],
		];
		$range4Pages20['Page 15 of 20 (range = 4)'] = [
			15,
			[
				2 => self::R3_BUTTON,
				3 => self::R2_BUTTON,
				4 => self::R0_BUTTON,
				5 => self::R0_BUTTON,
				7 => self::R0_BUTTON,
				8 => self::R0_BUTTON,
				9 => self::R2_BUTTON,
				10 => self::R2_BUTTON,
				11 => self::R2_DOTS,
			],
		];
		$range4Pages20['Page 16 of 20 (range = 4)'] = [
			16,
			[
				2 => self::R2_BUTTON,
				3 => self::R2_BUTTON,
				4 => self::R0_BUTTON,
				5 => self::R0_BUTTON,
				7 => self::R0_BUTTON,
				8 => self::R0_BUTTON,
				9 => self::R0_BUTTON,
				10 => self::R0_DOTS,
			],
		];
		$range4Pages20['Page 17 of 20 (range = 4)'] = [
			17,
			[
				2 => self::R2_BUTTON,
				3 => self::R0_BUTTON,
				4 => self::R0_BUTTON,
				5 => self::R0_BUTTON,
				7 => self::R0_BUTTON,
				8 => self::R0_BUTTON,
				9 => self::R0_DOTS,
			],
		];
		$range4Pages20['Page 18 of 20 (range = 4)'] = [
			18,
			[
				3 => self::R0_BUTTON,
				2 => self::R0_BUTTON,
				4 => self::R0_BUTTON,
				5 => self::R0_BUTTON,
			],
		];
		$range4Pages20['Page 19 of 20 (range = 4)'] = [
			19,
			[
				3 => self::R0_BUTTON,
				2 => self::R0_BUTTON,
				4 => self::R0_BUTTON,
				5 => self::R0_BUTTON,
			],
		];
		$range4Pages20['Page 20 of 20 (range = 4)'] = [
			20,
			[
				3 => self::R0_BUTTON,
				2 => self::R0_BUTTON,
				4 => self::R0_BUTTON,
				5 => self::R0_BUTTON,
			],
		];

		return $range4Pages20;
	}

	/**
	 * @param        mixed[] $buttonClasses
	 * @dataProvider generateDataProvider
	 */
	public function testGenerate(int $page, array $buttonClasses): void
	{
		$paginator = (new Paginator($page, 20))
			->addBreakpoint(3, self::R3_BUTTON, self::R3_DOTS)
			->addBreakpoint(2, self::R2_BUTTON, self::R2_DOTS)
			->addBreakpoint(0, self::R0_BUTTON, self::R0_DOTS)
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

	/**
	 * @return array<string, array<int>>
	 */
	public function argumentExceptionAddBreakpointProvider(): array
	{
		return [
			'Range set to -1' => [-1],
			'Range set to -10' => [-10],
		];
	}

	/**
	 * @dataProvider argumentExceptionAddBreakpointProvider
	 */
	public function testAddBreakpointExceptions(int $range): void
	{
		$paginator = new Paginator(10, 30);

		$this->expectException(\ResponzivePagination\Exceptions\InvalidArgumentException::class);
		$this->expectExceptionMessage('Parameter $range must be greater or equals to 0');

		$paginator->addBreakpoint($range, 'someClass', 'anotherClass');
	}
}
