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
 * Identification of JWT serializers
 *
 * Both JWS and JWE support three types of serializers:
 *  * a `Compact` serializer returning an URL-safe token for one signature/recipient
 *  * a `Global JSON` serializer returning a JSON representation, supporting multiple signatures/recipients
 *  * a `Flattened JSON` serializer returning a slightly simplified JSON for only one signature/recipient
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7516#section-7
 * @see https://datatracker.ietf.org/doc/html/rfc7515#section-7
 */
enum JWTSerializerEnum
{
	case JSON;
	case FLATTENED;
	case COMPACT;
}
