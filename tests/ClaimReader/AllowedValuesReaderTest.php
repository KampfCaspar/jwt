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

use KampfCaspar\JWT\ClaimReader\AllowedValuesReader;
use PHPUnit\Framework\TestCase;

class AllowedValuesReaderTest extends TestCase
{
	public function testRead(): void
	{
		$reader = new AllowedValuesReader();
		$this->assertNull($reader->read(17));
		$reader = new AllowedValuesReader([ 'one', 'two', 3]);
		$this->assertEquals('two', $reader->read('two'));
		$this->assertEquals(3, $reader->read(3));
		$this->assertNull($reader->read(3.0));
	}
}
