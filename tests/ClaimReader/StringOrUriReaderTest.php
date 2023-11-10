<?php
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\Test\JWT\ClaimReader;

use KampfCaspar\JWT\ClaimReader\StringOrUriReader;
use PHPUnit\Framework\TestCase;

class StringOrUriReaderTest extends TestCase
{

	public function testRead(): void
	{
		$stringable = new class() implements \Stringable {
			public function __toString(): string
			{
				return 'alpha';
			}
		};
		$reader = new StringOrUriReader();
		$this->assertSame('one', $reader->read('one'));
		$this->assertSame('3', $reader->read(3));
		$this->assertSame('3.1', $reader->read(3.1));
		$this->assertSame('alpha', $reader->read($stringable));
		$this->assertNull($reader->read(null));

		$reader = new StringOrUriReader('/^a/');
		$this->assertSame('alpha', $reader->read($stringable));
		$this->assertNull($reader->read('beta'));
		$this->assertNull($reader->read(3));
	}
}
