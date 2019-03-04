<?php
/**
 * Part of the GoCardless Pro PHP Client package integration for Laravel
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the MIT License.
 *
 * This source file is subject to the MIT License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Gocardless Laravel
 * @version    0.1.0
 * @author     Nested.net
 * @license    MIT
 * @link       https://nested.net
 */

namespace Nestednet\Gocardless\Facades;

use Illuminate\Support\Facades\Facade;

class Gocardless extends Facade
{
    /**
     *  {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'gocardless';
    }
}