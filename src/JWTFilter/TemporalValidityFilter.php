<?php declare(strict_types=1);
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\JWT\JWTFilter;

use KampfCaspar\Filter\ArrayFilter;
use Psr\Clock\ClockInterface;

class TemporalValidityFilter extends ArrayFilter
{
	public const DEFAULT_OPTIONS = [
		'mandatoryExpire' => false,
		'clock' => false,
		'leeway' => 120,
	] + parent::DEFAULT_OPTIONS;

	public function filterArray(\ArrayObject|array|\ArrayIterator &$object): array
	{
		$errors = [];
		$clock = $this->options['clock'];
		$now = match(true) {
			$clock instanceof ClockInterface => $clock->now()->getTimestamp(),
			$clock instanceof \DateTimeInterface => $clock->getTimestamp(),
			default => time()
		};
		$exp = $object['exp'] ?? null;
		if (is_null($exp) && $this->options['mandatoryExpire']) {
			$errors[] = $this->handleError('JWT does not have a mandatory expiration');
		}
		$leeway = $this->options['leeway'];
		if (!is_null($exp) && ($now - $leeway) > $exp) {
			$errors[] = $this->handleError(sprintf('JWT expired at %s',
				date('c', $exp)
			));
		}
		$nbf = $object['nbf'] ?? null;
		if (!is_null($nbf) && $now + $leeway < $nbf) {
			$errors[] = $this->handleError(sprintf('JWT only valid at %s',
				date('c', $nbf)
			));
		}
		return $errors;
	}
}