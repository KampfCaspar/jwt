<?php declare(strict_types=1);
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\JWT\ValidityChecker;

use KampfCaspar\JWT\JWT;
use KampfCaspar\JWT\ValidityChecker\ValidityCheckerInterface;

/**
 * Check Presence of Mandatory and Optional Claims
 */
class AllowedClaimsChecker implements ValidityCheckerInterface
{
	/**
	 * @param array<string> $mandatoryClaims
	 * @param array<string> $optionalClaims
	 */
	public function __construct(
		readonly protected array $mandatoryClaims = [],
		readonly protected array $optionalClaims = [],
	)
	{}

	public function falsify(JWT $jwt): ?string
	{
		$keys = array_keys($jwt->getArrayCopy());
		$missing = array_diff($this->mandatoryClaims, $keys);
		$surplus = array_diff($keys, $this->mandatoryClaims, $this->optionalClaims);

		$errors = [];
		if (count($missing)) {
			$errors[] = 'missing are ' . join( ', ', $missing);
		}
		if (count($surplus)) {
			$errors[] = 'unrecognized are ' . join( ', ', $surplus);
		}
		$res = $errors ? 'allowed claims mismatch: ' . join(' - ', $errors) : null;
		return $res;
	}
}