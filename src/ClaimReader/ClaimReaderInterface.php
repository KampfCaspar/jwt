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
 * Reader for Claum Values
 */
interface ClaimReaderInterface
{
	/**
	 * check claim value and return it in its proper form
	 *
	 * Method MUST NOT throw on invalid input but return null - it's on the container object
	 * to follow up.
	 *
	 * @return mixed    correct value or null on error
	 */
	public function read(mixed $value): mixed;
}