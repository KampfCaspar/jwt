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
 * Interface for prepared JWT Encoders
 *
 * The encoder object must be configured to encode an octet string to (either) JWT format upon
 * a single call to `encode`. Its pre-configuration would encompass keys, used cryptographic
 * algorithms and serialization.
 */
interface JWTEncoderInterface
{
	/** encode octet string to JWT
	 *
	 * Although JWT payload must consist of a JSON object, both JWS and JWE sign/encrypt
	 * any octet string. In tokens for internal use, one could use a different payload encoding.
	 *
	 * @see https://datatracker.ietf.org/doc/html/rfc7519#section-3
	 * @see https://datatracker.ietf.org/doc/html/rfc7516#section-1
	 * @see https://datatracker.ietf.org/doc/html/rfc7515#section-1
	 *
	 * @param string                   $payload         octets to include in the JWT
	 * @param array<string,mixed>      $header          (optional) additional common JWT header claims
	 * @param array<mixed>|string|null $additionalKeys  (optional) keys to sign/encode to, in addition to
	 *                                                  any default keys
	 * @param JWTSerializerEnum|null   $serializer      (optional) selection of non-default serializer
	 * @return string                                   JWT in octets
	 *
	 * @throws \InvalidArgumentException                if an invalid header or keys are given
	 * @throws \DomainException                         if encoder setup is wrong/incomplete
	 */
	public function encodeBinary(
		string $payload,
		array $header = [],
		array|string|null $additionalKeys = null,
		?JWTSerializerEnum $serializer = null
	): string;

	/** encode an array to JWT
	 *
	 * JWT payload must consist of a JSON object, represented by a PHP array.
	 *
	 * @param array<mixed>             $payload         octets to include in the JWT
	 * @param array<string,mixed>      $header          (optional) additional common JWT header claims
	 * @param array<mixed>|string|null $additionalKeys  (optional) keys to sign/encode to, in addition to
	 *                                                  any default keys
	 * @param JWTSerializerEnum|null   $serializer      (optional) selection of non-default serializer
	 * @return string                                   JWT in octets
	 *
	 * @throws \InvalidArgumentException                if an invalid header or keys are given
	 * @throws \DomainException                         if encoder setup is wrong/incomplete
	 */
	public function encode(
		array $payload,
		array $header = [],
		array|string|null $additionalKeys = null,
		?JWTSerializerEnum $serializer = null
	): string;
}