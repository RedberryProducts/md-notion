<?php

use RedberryProducts\MdNotion\Tests\TestCase;
use Saloon\Http\Faking\MockClient;

uses(TestCase::class)->in(__DIR__);

uses()
    ->beforeEach(function () {
        MockClient::destroyGlobal();
    })
    ->in(__DIR__);
