<?php
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\Test\JWT;

use KampfCaspar\JWT\JWT;
use PHPUnit\Framework\TestCase;

class JWTTest extends TestCase
{

	public function testOffsetSet(): void
	{
		$jwt = new JWT();
		$jwt['alpha'] = 'beta';
		$this->assertArrayHasKey('alpha', $jwt);
		$this->assertEquals('beta', $jwt['alpha']);
		$this->expectException(\BadMethodCallException::class);
		$jwt[new \DateTimeImmutable()] = 'errpr';
	}

	public function testSetClaims(): void
	{
		$jwt = new JWT(['iss' => 'alpha', 'sub' => 'beta']);
		$this->assertArrayHasKey('iss', $jwt);
		$this->assertArrayHasKey('sub', $jwt);
		$this->assertEquals('beta', $jwt['sub']);
	}

	public function testReaders(): void
	{
		$jwt = new JWT();
		$jwt->addReader('iss', fn(mixed $x) => $x + 1);
		$jwt->addReader('sub', fn(mixed $x) => null);

		$jwt['iss'] = 3;
		$this->assertEquals(4, $jwt['iss']);
		$jwt['other'] = 'alpha';
		$this->expectException(\InvalidArgumentException::class);
		$jwt['sub'] = 'beta';
	}

	public function testReaders2(): void
	{
		$jwt = new JWT();
		$this->expectException(\BadMethodCallException::class);
		$jwt->addReader([new \DateTimeImmutable()], fn(mixed $x) => $x + 1);
	}

	public function testGetHeaders(): void
	{
		$jwt = new JWT();
		$this->assertInstanceOf(JWT::class, $jwt->getHeaders());
	}

	public function testSetHeaders(): void
	{
		$jwt = new JWT();
		$jwt->setHeaders(['alg' => 'ES256', 'cty' => 'JWT']);
		$headers = $jwt->getHeaders();
		$this->assertArrayHasKey('alg', $headers);
		$this->assertArrayHasKey('cty', $headers);
		$headers = new JWT();
		$jwt->setHeaders($headers);
		$this->assertNotSame($headers, $jwt->getHeaders());
	}

	public function testAssertValidity(): void
	{
		$jwt = new JWT();
		$jwt->addValidityChecker( fn(JWT $x) => ($x['valid'] > 0 ? null : 'invalid'));
		$jwt['valid'] = 1;
		$jwt->assertValidity();
		$jwt['valid'] = -1;
		$this->expectException(\DomainException::class);
		$jwt->assertValidity();
	}

	public function test__toString(): void
	{
		$payload = [
			'alpha' => 'beta',
			'gamma' => 'delta',
		];
		$payload_json = json_encode($payload);
		$jwt = new JWT($payload);
		$this->assertJsonStringEqualsJsonString($payload_json, (string)$jwt);
	}

	public function testJsonSerialize(): void
	{
		$payload = [
			'alpha' => 'beta',
			'gamma' => 'delta',
		];
		$payload_json = json_encode($payload);
		$jwt = new JWT($payload);
		$this->assertJsonStringEqualsJsonString($payload_json, json_encode($jwt));
	}

}
