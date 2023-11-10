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

/**
 * Claim Reader That Only Allow Values from a Predetermined List
 */
class AllowedValuesReader implements ClaimReaderInterface
{
	/**
	 * @param array<mixed> $allowedValues
	 */
	public function __construct(
		readonly protected array $allowedValues = [],
	)
	{}
	public function read(mixed $value): mixed
	{
		return in_array($value, $this->allowedValues, true) ? $value : null;
	}
}