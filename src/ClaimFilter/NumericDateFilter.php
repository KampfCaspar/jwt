<?php declare(strict_types=1);
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\JWT\ClaimFilter;

use KampfCaspar\Filter\ValueFilter\IntegerValueFilter;
use Psr\Clock\ClockInterface;

class NumericDateFilter extends IntegerValueFilter
{
	protected function convertValue(mixed $value): int
	{
		if ($value instanceof ClockInterface) {
			$value = $value->now()->getTimestamp();
		}
		elseif ($value instanceof \DateTimeInterface) {
			$value = $value->getTimestamp();
		}
		return $value;
	}

}