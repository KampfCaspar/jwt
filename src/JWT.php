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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * @extends \ArrayObject<string,mixed>
 *
 * @property string $iss  Token Issuer
 * @property string $sub  Token Subject
 * @property string $aud  Token Audience
 * @property int    $iat  Token Issuance Timestamp (issued at)
 * @property int    $nbf  Token Start of Validity Timestamp (not before)
 * @property int    $exp  Token Expiry Timestamp (expire)
 */
class JWT extends \ArrayObject implements \Stringable, \JsonSerializable, LoggerAwareInterface
{
	use LoggerAwareTrait;

	/**
	 * list of claims defined in JWT RFC
	 * @see https://datatracker.ietf.org/doc/html/rfc7519#section-4.1
	 */
	final protected const CLAIMS_BASIC = [
		'iss', 'sub', 'aud',
		'iat', 'nbf', 'exp'
	];

	/**
	 * list of claims supported by the class (to be overridden)
	 */
	protected const CLAIMS = self::CLAIMS_BASIC;

	/**
	 * construct and optionally initialize JWT
	 *
	 * @param iterable<string,mixed> $claims
	 */
	public function __construct(Iterable $claims = [], ?LoggerInterface $logger = null) {
		parent::__construct([], self::ARRAY_AS_PROPS);
		if (!is_null($logger)) {
			$this->setLogger($logger);
		}
		$this->setClaims($claims);
	}

	/**
	 * verify claim name, the tupe of its value and possibly the value as well
	 *
	 * In subclasses, overwrite this method, check your own claims and then call parent
	 *
	 * @see https://datatracker.ietf.org/doc/html/rfc7519#section-4.1
	 *
	 * @return mixed  claim value as correct type
	 * @throws \InvalidArgumentException  if value cannot be cast and is somehow invalid
	 */
	protected function filterClaim(string $claim, mixed $value): mixed
	{
		switch ($claim) {
			case 'iss':
			case 'sub':
			case 'aud':
			case 'jti':
				return (string)$value;
			case 'exp':
			case 'nbf':
			case 'iat':
				if ($value instanceof \DateTimeInterface) {
					return $value->getTimestamp();
				}
				if (is_numeric($value)) {
					return (int)$value;
				}
				throw new \InvalidArgumentException(sprintf(
					'JWT claim "%s" must be parseable as NumericDate',
					$claim
				));
		}
		$this->logger?->info('unverified JWT claim {claim}', ['claim' => $claim]);

		return $value;
	}

	/**
	 * assert that key is string and check if key is available in claims list
	 */
	protected function assertKey(mixed $key): void
	{
		if (!is_string($key)) {
			throw new \BadMethodCallException(sprintf(
				'JWT only supports string key, %s given',
				gettype($key)
			));
		}
		if (!in_array($key, static::CLAIMS, true)) {
			$this->logger?->info('unlisted JWT claim {claim}', ['claim' => $key]);
		}
	}

	public function offsetExists(mixed $key): bool
	{
		$this->assertKey($key);
		return parent::offsetExists($key);
	}

	public function offsetGet(mixed $key): mixed
	{
		$this->assertKey($key);
		return parent::offsetGet($key);
	}

	public function offsetSet(mixed $key, mixed $value): void
	{
		$this->assertKey($key);
		// @phpstan-ignore-next-line as $key definitely is no longer null
		$value = $this->filterClaim($key, $value);
		parent::offsetSet($key, $value);
	}

	public function offsetUnset(mixed $key): void
	{
		$this->assertKey($key);
		parent::offsetUnset($key);
	}

	/**
	 * set claim (offset) with fluent interface
	 */
	public function setClaim(string $key, mixed $value): static
	{
		$this->offsetSet($key, $value);
		return $this;
	}

	/**
	 * set multiple claims with a fluent interface
	 * @param iterable<string,mixed> $claims
	 */
	public function setClaims(Iterable $claims): static
	{
		foreach ($claims as $claim => $value) {
			$this->offsetSet($claim, $value);
		}
		return $this;
	}

	public function jsonSerialize(): mixed
	{
		return $this->getArrayCopy();
	}

	public function __toString(): string
	{
		try {
			return \json_encode($this->getArrayCopy(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
		catch (\Throwable $e) {
			throw new \DomainException('error encoding to json', $e->getCode(), $e);
		}
	}

}