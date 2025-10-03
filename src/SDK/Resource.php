<?php

namespace Redberry\MdNotion\SDK;

use Saloon\Http\Connector;

class Resource
{
    public function __construct(
        protected Connector $connector,
    ) {}
}
