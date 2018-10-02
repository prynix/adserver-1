<?php
/**
 * Copyright (c) 2018 Adshares sp. z o.o.
 *
 * This file is part of AdServer
 *
 * AdServer is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * AdServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AdServer.  If not, see <https://www.gnu.org/licenses/>
 */

namespace Adshares\Adserver\Models\Traits;

use Adshares\Adserver\Utilities\AdsUtils;

/**
 * binhex columns.
 */
trait TransactionId
{
    public function transactionIdMutator($key, $value)
    {
        $this->attributes[$key] = null !== $value ? hex2bin(AdsUtils::decodeTransactionId($value)) : null;
    }

    public function transactionIdAccessor($value)
    {
        return null === $value ? null : AdsUtils::encodeTransactionId(bin2hex($value));
    }
}