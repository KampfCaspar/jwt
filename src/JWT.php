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

use KampfCaspar\Filter\ArrayFilter;
use KampfCaspar\Filter\ArrayFilterInterface;
use KampfCaspar\Filter\Exception\FilteringException;
use KampfCaspar\Filter\ValueFilter;
use KampfCaspar\Filter\ValueFilterInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * JSON Object Payload and JWT Headers
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

	/** Array of Claim Filters
	 * @var array<string,ValueFilterInterface>
	 * @see self::addClaimFilter()
	 */
	protected array $claimFilters = [];

	/** ArrayFilter to Ensure JWT Validity
	 * @see self::setValidator()
	 */
	protected ArrayFilterInterface $validator;

	/** Collection for JWT Header
	 * @see self::setHeader()
	 */
	protected JWT $header;

	/** Construct New JWT
	 * @param iterable<string,mixed> $claims
	 */
	public function __construct(Iterable $claims = [], ?LoggerInterface $logger = null) {
		parent::__construct([], self::ARRAY_AS_PROPS);
		if (!is_null($logger)) {
			$this->setLogger($logger);
		}
		$this->setClaims($claims);
	}

	/** Get JWT Header Collection
	 */
	public function getHeader(): JWT
	{
		if (!isset($this->header)) {
			$this->header = new static(logger: $this->logger);
		}
		return $this->header;
	}

	/** Set a New JWT Header Collection
	 * @param JWT|iterable<string,mixed> $header
	 */
	public function setHeader(JWT|Iterable $header): static
	{
		$this->header = $header instanceof JWT ? $header : new static($header, $this->logger);
		return $this;
	}

	/**
	 * Add a ValueFilter To One/Many Claims
	 * @param string|iterable<string> $claims
	 */
	public function addClaimFilter(string|iterable $claims, mixed $filter): static
	{
		$filter = ValueFilter::createFilter($filter);
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
			$this->claimFilters[$claim] = $filter;
		}
		return $this;
	}

	/**
	 * Filter a Value While Setting the Claim
	 * @throws \InvalidArgumentException  if value is somehow invalid and uncorrectable
	 */
	protected function filterClaim(string $claim, mixed $value): mixed
	{
		$filter = $this->claimFilters[$claim] ?? null;
		if ($filter) {
			try {
				$value = $filter->filterValue($value);
			}
			catch (FilteringException $e) {
				throw new \InvalidArgumentException(sprintf(
					'invalid value for claim %s on JWT with ID %s',
					$claim,
					$this->offsetGet('jti') ?? '(unset)'
				), previous: $e);
			}
			if (is_null($value)) {
				$this->logger?->error('invalid value for claim {claim} on JWT with ID {jti}', [
					'claim' => $claim,
					'jti' => $this->offsetGet('jti') ?? '(unset)'
				]);
			}
		}
		else {
			$this->logger?->info('unfiltered claim {claim} on JWT with ID {jti}', [
				'claim' => $claim,
				'jti' => $this->offsetGet('jti') ?? '(unset)'
			]);
		}

		return $value;
	}

	/** Set a Claim to a Specific Value
	 */
	public function offsetSet(mixed $key, mixed $value): void
	{
		if (!is_string($key)) {
			throw new \BadMethodCallException(sprintf(
				'JWT only supports string key, %s given',
				gettype($key)
			));
		}
		if (!is_null($value)) {
			$value = $this->filterClaim($key, $value);
		}
		if (is_null($value)) {
			$this->offsetUnset($key);
		}
		else {
			parent::offsetSet($key, $value);
		}
	}

	/**
	 * Set Multiple Claims
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
	 * Set the ArrayFilter that checks JWT for its Validity
	 */
	public function setValidator(mixed $checker): static
	{
		$this->validator = ArrayFilter::createFilter($checker);
		return $this;
	}

	/**
	 * Assert Validity of JWT
	 */
	public function validate(): void
	{
		if (isset($this->validator)) {
			try {
				$errors = $this->validator->filterArray($this);
			}
			catch (FilteringException $e) {
				throw new \DomainException(sprintf(
					'invalid JWT with ID %s',
					$this->offsetGet('jti') ?? '(unset)'
				), $e->getCode(), $e);
			}
			if ($errors) {
				$this->logger?->warning('invalid JWT with ID {jti}', [
					'errors' => $errors,
					'jti' => $this->offsetGet('jti') ?? '(unset)'
				]);
			}
		}
		else {
			$this->logger?->info('JWT with ID {jti} has no validator', [
				'jti' => $this->offsetGet('jti') ?? '(unset)'
			]);
		}
	}

	public function jsonSerialize(): mixed
	{
		return $this->getArrayCopy();
	}

	public function __toString(): string
	{
		try {
			return json_encode($this, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
		// @codeCoverageIgnoreStart
		catch (\Throwable $e) {
			throw new \LogicException('error encoding to json', $e->getCode(), $e);
		}
		// @codeCoverageIgnoreEnd
	}

}