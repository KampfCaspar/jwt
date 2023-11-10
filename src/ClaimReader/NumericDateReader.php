<?php declare(strict_types=1);
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\JWT\ClaimReader;

use KampfCaspar\JWT\ClaimReader\ClaimReaderInterface;
use Psr\Clock\ClockInterface;

/**
 * Claim Reader That Ensures Value is a NumericDate, optionally with min/max
 */
class NumericDateReader implements ClaimReaderInterface
{
	public function __construct(
		readonly protected int $min = 0,
		readonly protected int $max = PHP_INT_MAX,
	)
	{}

	public function read(mixed $value): mixed
	{
		if ($value instanceof ClockInterface) {
			$value = $value->now()->getTimestamp();
		}
		elseif ($value instanceof \DateTimeInterface) {
			$value = $value->getTimestamp();
		}
		$value = filter_var(
			$value,
			FILTER_VALIDATE_INT,
			[
				'options' => [
					'min_range' => $this->min,
					'max_range' => $this->max,
				],
				'flags' => FILTER_NULL_ON_FAILURE | FILTER_REQUIRE_SCALAR,
			]
		);
		return $value;
	}
}