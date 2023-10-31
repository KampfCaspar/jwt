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
 * Basic Trait for JWT Decoders
 *
 * Provides an implementation for JSON decoding the payload.
 * Therefore, only the octet decoding has to be implemented in class
 */
trait JWTDecoderTrait
{
	abstract public function decodeBinary(string $token): string;

	public function decode(string $token): array {
		$payload = $this->decodeBinary($token);
		$res = json_decode($payload, true, 512, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		if ($res === false) {
			throw new \InvalidArgumentException('JWT seems not to contain JSON');
		}
		return $res;
	}

}