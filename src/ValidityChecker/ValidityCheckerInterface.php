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

/**
 * Validity Checker for JWT
 *
 * A {@see JWT} can hold one or more ValidityCheckers that inspect the JWT as a whole.
 */
interface ValidityCheckerInterface
{
	/**
	 * check the validity of the JWT and return an error message on falsification
	 *
	 * This method MUST NOT throw on falsification but only return the error message.
	 * It's upon the container object to follow up on errors.
	 *
	 * @return ?string  error message or null if no error is found
	 */
	public function falsify(JWT $jwt): ?string;
}