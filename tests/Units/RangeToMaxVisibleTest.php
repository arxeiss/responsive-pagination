<?php

declare(strict_types=1);

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use ResponzivePagination\Paginator;

final class RangeToMaxVisibleTest extends TestCase
{
	public function testConvertionFromRangeToMaxVisible(): void
	{
		$this->assertEquals(7, Paginator::rangeToMaxVisible(1));
		$this->assertEquals(9, Paginator::rangeToMaxVisible(2));
		$this->assertEquals(11, Paginator::rangeToMaxVisible(3));
		$this->assertEquals(13, Paginator::rangeToMaxVisible(4));
		$this->assertEquals(15, Paginator::rangeToMaxVisible(5));
		$this->assertEquals(17, Paginator::rangeToMaxVisible(6));
	}

	/**
	 * @return array<string, array<int>>
	 */
	public function exceptionConvertionProvider(): array
	{
		return [
			'Range set to -1' => [-1],
			'Range set to -10' => [-10],
		];
	}

	/**
	 * @dataProvider exceptionConvertionProvider
	 */
	public function testExceptions(int $range): void
	{
		$this->expectException(\ResponzivePagination\Exceptions\InvalidArgumentException::class);
		$this->expectExceptionMessage('Parameter $range must be greater or equals to 0');

		Paginator::rangeToMaxVisible($range);
	}
}
