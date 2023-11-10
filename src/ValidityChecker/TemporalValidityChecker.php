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
use Psr\Clock\ClockInterface;

/**
 * Check if JWT is in its validity period
 */
class TemporalValidityChecker implements ValidityCheckerInterface
{
	public function __construct(
		protected bool            $mustExpire = true,
		protected int             $leeway = 30,
		protected ?ClockInterface $clock = null,
	)
	{}

	public function falsify(JWT $jwt): ?string
	{
		$now = $this->clock?->now()->getTimestamp() ?? time();
		$exp = $jwt['exp'] ?? null;
		if ($this->mustExpire && is_null($exp)) {
			return 'JWT is missing a mandatory expiration';
		}
		if (!is_null($exp) && ($exp < $now - $this->leeway)) {
			return sprintf('JWT expired at %s',
				(new \DateTime())->setTimestamp($exp)->format('c'));
		}
		$nbf = $jwt['nbf'] ?? 0;
		if ($nbf > $now + $this->leeway) {
			return sprintf('JWT is only valid on/after %s',
				(new \DateTime())->setTimestamp($nbf)->format('c'));
		}
		return null;
	}
}