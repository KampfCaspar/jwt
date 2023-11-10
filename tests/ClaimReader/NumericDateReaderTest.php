<?php declare(strict_types=1);
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\Test\JWT\ClaimReader;

use KampfCaspar\JWT\ClaimReader\NumericDateReader;
use KampfCaspar\Test\JWT\Fixtures\FixedClock;
use PHPUnit\Framework\TestCase;

class NumericDateReaderTest extends TestCase
{

	public function testRead(): void
	{
		$reader = new NumericDateReader();
		$this->assertEquals(1699306654, $reader->read(1699306654));
		$this->assertNull($reader->read('wrong'));
		$this->assertNull($reader->read([1699306654, 1699306654]));
		$reader = new NumericDateReader(1699306000, 1699307000);
		$this->assertEquals(1699306654, $reader->read(1699306654));
		$this->assertNull($reader->read(1699305000));
		$this->assertNull($reader->read(1699307001));
	}

	public function testReadSpecialCases(): void
	{
		$reader = new NumericDateReader();
		$this->assertEquals(
			1699306654,
			$reader->read((new \DateTime())->setTimestamp(1699306654)));
		$this->assertEquals(
			1699306654,
			$reader->read(new FixedClock(1699306654)));
	}
}
