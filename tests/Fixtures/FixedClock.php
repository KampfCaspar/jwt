<?php declare(strict_types=1);
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\Test\JWT\Fixtures;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

class FixedClock implements ClockInterface
{
	protected \DateTimeImmutable $dt;

	public function __construct(int $timestamp)
	{
		$this->dt = (new \DateTimeImmutable())->setTimestamp($timestamp);
	}

	public function now(): \DateTimeImmutable
	{
		return $this->dt;
	}
}