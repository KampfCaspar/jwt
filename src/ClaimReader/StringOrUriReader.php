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

/**
 * Claim Reader Ensuring Value is a String, optionally matching a regex
 */
class StringOrUriReader implements ClaimReaderInterface
{
	public function __construct(
		protected ?string $regex = null,
	)
	{}

	public function read(mixed $value): mixed
	{
		if ($this->regex) {
			$value = filter_var($value, FILTER_VALIDATE_REGEXP, [
				'options' => [
					'regexp' => $this->regex,
				],
				'flags' => FILTER_NULL_ON_FAILURE | FILTER_REQUIRE_SCALAR
			]);
		}
		elseif (!is_null($value)) {
			$value = (string)$value;
		}
		return $value;
	}
}