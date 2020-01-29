<?php

declare(strict_types=1);

namespace Tests\Units;

use PHPUnit\Framework\TestCase;
use ResponsivePagination\Paginator;

final class LinkFormatTest extends TestCase
{
	public function testDefaultLinkFormat(): void
	{
		$paginator = (new Paginator(5, 10, 3))->generate();

		$this->assertSame('?page=4', $paginator->prev->link);
		$this->assertSame('?page=5', $paginator->current->link);
		$this->assertSame('?page=6', $paginator->next->link);

		for ($i = 0; $i < 8; $i += 1) {
			$this->assertSame('?page=' . ($i + 1), $paginator->buttons[$i]->link);
		}
		$this->assertSame('?page=10', $paginator->buttons[9]->link);
	}

	public function testCustomLinkFormatWithSearch(): void
	{
		$paginator = (
			new Paginator(
				5,
				30,
				3,
				\sprintf(
					'?search=%s&page=%s',
					\urlencode("Searched ' by %%page%% User"),
					'%%page%%'
				)
			)
		)->generate();

		$this->assertSame('?search=Searched+%27+by+%25%25page%25%25+User&page=4', $paginator->prev->link);
		$this->assertSame('?search=Searched+%27+by+%25%25page%25%25+User&page=5', $paginator->current->link);
		$this->assertSame('?search=Searched+%27+by+%25%25page%25%25+User&page=6', $paginator->next->link);

		for ($i = 0; $i < 8; $i += 1) {
			$this->assertSame(
				'?search=Searched+%27+by+%25%25page%25%25+User&page=' . ($i + 1),
				$paginator->buttons[$i]->link
			);
		}
		$this->assertSame('?search=Searched+%27+by+%25%25page%25%25+User&page=30', $paginator->buttons[9]->link);
	}

	public function testCustomLinkDifferentForFirstPage(): void
	{
		$paginator = (new Paginator(2, 10, 3, '?strana=%%page%%', ''))->generate();

		$this->assertSame('', $paginator->prev->link);
		$this->assertSame('?strana=2', $paginator->current->link);
		$this->assertSame('?strana=3', $paginator->next->link);

		$this->assertSame('', $paginator->buttons[0]->link);
		for ($i = 1; $i < 6; $i += 1) {
			$this->assertSame('?strana=' . ($i + 1), $paginator->buttons[$i]->link);
		}
		$this->assertSame('?strana=10', $paginator->buttons[7]->link);
	}
}
