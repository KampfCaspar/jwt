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
 * Interface for Prepared JWT Encoders
 *
 * The encoder object must be configured for encoding to the supported JWT format
 * and can then be given to any consumer that only calls `encode` with its individual
 * payload.
 * The configuration would encompass keys, used cryptographic algorithms and serialization.
 * The consumer generally will not change the configuration but may present additional keys
 * (allows for personalized encryption per recipient),
 */
interface JWTEncoderInterface
{
	/** encode an array|JWT payload or binary string to JWT in token form
	 *
	 * JWTs should consist of a JSON object but both JWS and JWE support any octet string.
	 * It is therefore possible to use any payload type, e.g. for internal uses.
	 *
	 * The encode call accepts either a PHP array or {@see JWT} that will be encoded
	 * in JSON - or alternatively just a binary string that is taken verbatim.
	 *
	 * @see https://datatracker.ietf.org/doc/html/rfc7519#section-3
	 * @see https://datatracker.ietf.org/doc/html/rfc7516#section-1
	 * @see https://datatracker.ietf.org/doc/html/rfc7515#section-1
	 *
	 * @param array<mixed>|JWT|string  $payload         claims to encode
	 * @param array<string,mixed>      $header          (optional) additional common JWT header claims
	 * @param array<mixed>|string|null $additionalKeys  (optional) keys to sign/encode to, in addition to
	 *                                                  any default keys
	 * @param JWTSerializerEnum|null   $serializer      (optional) selection of non-default serializer
	 * @return string                                   JWT token in octets
	 *
	 * @throws \InvalidArgumentException                if an invalid header or keys are given
	 * @throws \DomainException                         if encoder setup is wrong/incomplete
	 */
	public function encode(
		array|JWT|string $payload,
		array $header = [],
		array|string|null $additionalKeys = null,
		?JWTSerializerEnum $serializer = null
	): string;
}