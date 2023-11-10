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
use KampfCaspar\JWT\ValidityChecker\TemporalValidityChecker;
use KampfCaspar\Test\JWT\Fixtures\FixedClock;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class TemporalValidityCheckerTest extends TestCase
{
	public const TS = 1699306654;
	public function testFalsify(): void
	{
		$clock = new FixedClock(self::TS);
		$checker = new TemporalValidityChecker(leeway: 10, clock: $clock);
		$jwt = new JWT();

		$jwt['exp'] = 2 * self::TS; // far into the future
		$jwt['nbf'] = self::TS + 11;
		$this->assertIsString($checker->falsify($jwt)); // too early
		$jwt['nbf'] = self::TS + 5;
		$this->assertNull($checker->falsify($jwt)); // leeway
		$jwt['nbf'] = self::TS - 11;
		$this->assertNull($checker->falsify($jwt)); // valid

		$jwt['exp'] = self::TS - 5;
		$this->assertNull($checker->falsify($jwt)); // expired in leeway
		$jwt['exp'] = self::TS - 11;
		$this->assertIsString($checker->falsify($jwt)); // expired
		$jwt['exp'] = self::TS + 11;
		$this->assertNull($checker->falsify($jwt)); // unexpired

		unset($jwt['exp']);
		$this->assertIsString($checker->falsify($jwt)); // must expire
		$checker = new TemporalValidityChecker(mustExpire: false, clock: $clock);
		$this->assertNull($checker->falsify($jwt)); // no expiry
	}
}
