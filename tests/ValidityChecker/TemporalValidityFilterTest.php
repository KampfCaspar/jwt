<?php declare(strict_types=1);
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\Test\JWT\ValidityChecker;

use KampfCaspar\JWT\JWT;
use KampfCaspar\JWT\JWTFilter\TemporalValidityFilter;
use KampfCaspar\JWT\ValidityChecker\TemporalValidityChecker;
use KampfCaspar\Test\JWT\Fixtures\FixedClock;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class TemporalValidityFilterTest extends TestCase
{
	public const TS = 1699306654;
	public function testFalsify(): void
	{
		$clock = new FixedClock(self::TS);
		$filter = new TemporalValidityFilter([
			TemporalValidityFilter::OPTION_SOFT_FAILURE => true,
			'mandatoryExpire' => true,
			'clock' => $clock,
			'leeway' => 10,
		]);

		$arr = [
			'exp' => 2 * self::TS,
			'nbf' => self::TS + 11
		];
		self::assertNotEmpty($filter->filterArray($arr));
		$arr['nbf'] = self::TS + 5;
		self::assertEmpty($filter->filterArray($arr));
		$arr['nbf'] = self::TS - 11;
		self::assertEmpty($filter->filterArray($arr));

		$arr['exp'] = self::TS - 5;
		self::assertEmpty($filter->filterArray($arr));
		$arr['exp'] = self::TS - 11;
		self::assertNotEmpty($filter->filterArray($arr));
		$arr['exp'] = self::TS + 11;
		self::assertEmpty($filter->filterArray($arr));

		unset($arr['exp']);
		self::assertNotEmpty($filter->filterArray($arr));
		$filter->setOptions([
			'mandatoryExpire' => false,
		]);
		self::assertEmpty($filter->filterArray($arr));
	}
}
