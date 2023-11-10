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
 * Interface for Prepared JWT Decoders
 *
 * The decoder object must be configured for decoding from the supported JWT format
 * and can then be given to any consumer that only calls `decode` with its individual
 *  payload.
 *  The configuration would encompass keys, used cryptographic algorithms and serialization.
 *  The consumer will not change the configuration.
 */
interface JWTDecoderInterface
{
	/** decode a JWT in token form to an octet string
	 *
	 * @param string $token               JWT token octet string
	 * @return array{mixed, array<mixed>} an array of binary payload and header array
	 *
	 * @throws \InvalidArgumentException  if an invalid token is given or it does not validate
	 * @throws \DomainException           if encoder setup is wrong/incomplete
	 */
	public function decodeBinary(string $token): array;

	/** decode a JWT in token form to a PHP array and optionally loads it into a JWT object
	 *
	 * JWT payload must consist of a JSON object, representable in a PHP array.
	 * If a prepared {@see JWT} is given, it's filled with body and header claims
	 *
	 * @param string $token JWT octet string
	 * @return array<mixed> JWT payload
	 *
	 * @throws \InvalidArgumentException  if an invalid token is given or it does not validate
	 * @throws \DomainException           if encoder setup is wrong/incomplete
	 */
	public function decode(string $token, ?JWT $jwt = null): array;
}