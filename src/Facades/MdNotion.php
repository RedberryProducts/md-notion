<?php

namespace Redberry\MdNotion\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Redberry\MdNotion\MdNotion
 */
class MdNotion extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Redberry\MdNotion\MdNotion::class;
    }
}
