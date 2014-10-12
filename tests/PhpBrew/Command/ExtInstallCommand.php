<?php
use CLIFramework\Testing\CommandTestCase;

class InstallCommandTest extends CommandTestCase
{
    protected $root;
    protected $home;
    protected $extini;

    public function setUp()
    {
        $this->root = $this->home = getcwd() . '/tests/.phpbrew';
        $this->extini = $this->root . '/php/5.4.29/var/db/xdebug.ini';
    }

    public function setupApplication() {
        putenv('PHPBREW_ROOT=' . $this->root);
        putenv('PHPBREW_HOME=' . $this->home);
        return new PhpBrew\Console;
    }

    /**
     * @outputBuffering enabled
     * @depends testInstallCommand
     */
    public function testExtInstallCommand()
    {
        $this->runCommand('phpbrew -d install 5.4.29');
        $this->runCommand('phpbrew ext install xdebug');
        $this->assertFileExists($this->extini);
    }

    /**
     * When reinstalling extension ini file should be preserved as it is
     *
     * @outputBuffering enabled
     * @depends testExtInstallCommand
     */
    public function textExtReinstall()
    {
        file_put_contents($this->extini, "\n; custom");
        $this->runCommand('phpbrew ext install xdebug');
        $this->assertFileExists($this->extini);
        $this->assertContains("\n; custom", file_get_contents($this->extini));
    }
}

