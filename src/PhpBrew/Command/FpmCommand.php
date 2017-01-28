<?php

namespace PhpBrew\Command;

use CLIFramework\Command;
use CLIFramework\ValueCollection;


class FpmCommand extends Command
{
    public function brief()
    {
        return 'fpm commands';
    }

    public function init()
    {
        parent::init();
        $this->command('setup');
        $this->command('start');
        $this->command('stop');
    }

    public function execute()
    {
    }
}

