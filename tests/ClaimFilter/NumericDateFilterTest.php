<?php declare(strict_types=1);
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\Test\JWT\ClaimFilter;

use KampfCaspar\JWT\ClaimFilter\NumericDateFilter;
use KampfCaspar\Test\JWT\Fixtures\FixedClock;
use PHPUnit\Framework\TestCase;

class NumericDateFilterTest extends TestCase
{

	public function testFilterValue(): void
	{
		$filter = new NumericDateFilter([
			NumericDateFilter::OPTION_SCALARITY => null,
			NumericDateFilter::OPTION_SOFT_FAILURE => true,
		]);
		self::assertIsInt($filter->filterValue(16000000));
		self::assertIsInt($filter->filterValue(new FixedClock(16000000)));
		self::assertIsInt($filter->filterValue(new \DateTimeImmutable('now')));
	}
}
