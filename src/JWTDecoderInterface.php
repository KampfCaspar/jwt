<?php declare(strict_types=1);
/**
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * @license AGPL-3.0-or-later
 * @author KampfCaspar <code@kampfcaspar.ch>
 */

namespace KampfCaspar\JWT;

/**
 * Interface for prepared JWT Decoders
 *
 * The decoder object must be configured to decode a JWT upon a single call to `encode`.
 * Its pre-configuration would encompass keys, used cryptographic
 * algorithms and serialization.
 */
interface JWTDecoderInterface
{
	/** decode a JWT to an octet string
	 *
	 *  Although JWT payload must consist of a JSON object, both JWS and JWE sign/encrypt
	 *  any octet string. In tokens for internal use, one could use a different payload encoding.
	 *
	 * @see https://datatracker.ietf.org/doc/html/rfc7519#section-3
	 * @see https://datatracker.ietf.org/doc/html/rfc7516#section-1
	 * @see https://datatracker.ietf.org/doc/html/rfc7515#section-1
	 *
	 * @param string $token JWT octet string
	 * @return string       JWT payload as octet string
	 *
	 * @throws \InvalidArgumentException  if an invalid token is given or it does not validate
	 * @throws \DomainException           if encoder setup is wrong/incomplete
	 */
	public function decodeBinary(string $token): string;

	/** decode a JWT to a PHP array
	 *
	 * JWT payload must consist of a JSON object, represented by a PHP array.
	 *
	 * @param string $token JWT octet string
	 * @return array<mixed> JWT payload
	 *
	 * @throws \InvalidArgumentException  if an invalid token is given or it does not validate
	 * @throws \DomainException           if encoder setup is wrong/incomplete
	 */
	public function decode(string $token): array;
}