<?php

namespace Redberry\MdNotion\Commands;

use Illuminate\Console\Command;

class MdNotionCommand extends Command
{
    public $signature = 'md-notion';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
