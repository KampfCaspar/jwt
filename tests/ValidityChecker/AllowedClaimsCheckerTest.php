<?php
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
use KampfCaspar\JWT\ValidityChecker\AllowedClaimsChecker;
use PHPUnit\Framework\TestCase;

class AllowedClaimsCheckerTest extends TestCase
{

	public function testFalsify(): void
	{
		$checker = new AllowedClaimsChecker([
			'iss'
		], [
			'iat'
		]);
		$jwt = new JWT();
		$this->assertIsString($checker->falsify($jwt));
		$jwt['iss'] = 'a';
		$this->assertNull($checker->falsify($jwt));
		$jwt['iat'] = 3;
		$this->assertNull($checker->falsify($jwt));
		$jwt['nbf'] = 3;
		$this->assertIsString($checker->falsify($jwt));
	}
}
