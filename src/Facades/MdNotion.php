<?php

namespace RedberryProducts\MdNotion\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RedberryProducts\MdNotion\MdNotion
 */
class MdNotion extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RedberryProducts\MdNotion\MdNotion::class;
    }
}
