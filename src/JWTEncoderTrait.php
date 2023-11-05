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
 * Basic Trait for JWT Encoders
 *
 * Provides an implementation for JSON encoding the payload.
 * Therefore, only the octet encoding has to be implemented in class
 */

trait JWTEncoderTrait
{
	abstract public function encodeBinary(
		string $payload,
		array $header = [],
		array|string|null $additionalKeys = null,
		?JWTSerializerEnum $serializer = null
	): string;

	public function encode(
		array|JWT $payload,
		array $header = [],
		array|string|null $additionalKeys = null,
		?JWTSerializerEnum $serializer = null
	): string
	{
		$json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		if ($json === false) {
			throw new \DomainException(sprintf('error encoding JWT payload to JSON: %s', json_last_error_msg()));
		}
		return $this->encodeBinary(
			$json,
			$header,
			$additionalKeys,
			$serializer
		);
	}
}