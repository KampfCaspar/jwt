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

use KampfCaspar\JWT\ClaimReader\ClaimReaderInterface;
use KampfCaspar\JWT\ValidityChecker\ValidityCheckerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class representing a JWT JSON payload and optionally corresponding headers
 *
 * @extends \ArrayObject<string,mixed>
 *
 * minimal claim set
 * @property string $iss  Token Issuer
 * @property string $sub  Token Subject
 * @property string $aud  Token Audience
 * @property int    $iat  Token Issuance Timestamp (issued at)
 * @property int    $nbf  Token Start of Validity Timestamp (not before)
 * @property int    $exp  Token Expiry Timestamp (expire)
 *
 * @phpstan-consistent-constructor
 */
class JWT extends \ArrayObject implements \Stringable, \JsonSerializable, LoggerAwareInterface
{
	use LoggerAwareTrait;

	/** collection of all claim readers
	 * @var array<ClaimReaderInterface|callable>
	 * @see self::addReader()
	 */
	protected array $readers = [];

	/** collection of all validty checkers
	 * @var array<ValidityCheckerInterface|callable>
	 * @see self::addValidityChecker()
	 */
	protected array $checkers = [];

	/** collection of all header claims
	 * @var JWT
	 */
	protected JWT $headers;

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
	 * get headers collection
	 */
	public function getHeaders(): JWT
	{
		if (!isset($this->headers)) {
			$this->headers = new static(logger: $this->logger);
		}
		return $this->headers;
	}

	/**
	 * set new headers collection
	 * @param JWT|iterable<string,mixed> $headers
	 */
	public function setHeaders(JWT|Iterable $headers): static
	{
		$this->headers = $headers instanceof JWT ? clone $headers : new static($headers, $this->logger);
		return $this;
	}

	/**
	 * add a claim reader for a field
	 *
	 * Each field (claim) can have a reader associated that ensures values set are
	 * compatible.
	 * @see ClaimReaderInterface
	 *
	 * @param string|iterable<string> $claims
	 */
	public function addReader(string|Iterable $claims, ClaimReaderInterface|callable $reader): static
	{
		if (is_string($claims)) {
			$claims = (array)$claims;
		}
		foreach ($claims as $claim) {
			if (!is_string($claim)) {
				throw new \BadMethodCallException(sprintf(
					'JWT claim names must be string, got %s',
					gettype($claim)
				));
			}
			$this->readers[$claim] = $reader;
		}
		return $this;
	}

	/**
	 * verify the tupe of claim value and possibly the value as well
	 *
	 * @return mixed  claim value as correct type
	 * @throws \InvalidArgumentException  if value cannot be cast and is somehow invalid
	 */
	protected function filterClaim(string $claim, mixed $value): mixed
	{
		$type = is_object($value) ? get_class($value) : gettype($value);
		$reader = $this->readers[$claim] ?? null;
		if ($reader) {
			$value = is_callable($reader) ? $reader($value) : $reader->read($value);
		}
		else {
			$this->logger?->info('unlisted JWT claim {claim}', [
				'claim' => $claim,
				'value' => $value,
			]);
		}
		if (is_null($value)) {
			throw new \InvalidArgumentException(sprintf(
				'Reading JWT claim "%s" failed with a "%s"',
				$claim,
				$type
			));
		}

		return $value;
	}

	/**
	 * set a field (claim) to a specific value
	 */
	public function offsetSet(mixed $key, mixed $value): void
	{
		if (!is_string($key)) {
			throw new \BadMethodCallException(sprintf(
				'JWT only supports string key, %s given',
				gettype($key)
			));
		}
		$value = $this->filterClaim($key, $value);
		parent::offsetSet($key, $value);
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

	/**
	 * add a validity checker to test the whole JWT upon request
	 * @see ValidityCheckerInterface
	 * @see self::assertValidity()
	 */
	public function addValidityChecker(ValidityCheckerInterface|callable $checker): static
	{
		$this->checkers[] = $checker;
		return $this;
	}

	/**
	 * assert validity of JWT by calling all ValidityCheckers
	 */
	public function assertValidity(): void
	{
		$errors = [];
		foreach ($this->checkers as $checker) {
			/** @var ?string $error */
			$error = is_callable($checker) ? $checker($this) : $checker->falsify($this);
			if ($error) {
				$errors[] = $error;
			}
		}
		if ($errors) {
			throw new \DomainException(join('; ', $errors));
		}
	}

	public function jsonSerialize(): mixed
	{
		return $this->getArrayCopy();
	}

	public function __toString(): string
	{
		try {
			return \json_encode($this, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
		// @codeCoverageIgnoreStart
		catch (\Throwable $e) {
			throw new \DomainException('error encoding to json', $e->getCode(), $e);
		}
		// @codeCoverageIgnoreEnd
	}

}