<?php
use CLIFramework\Logger;
use PhpBrew\Build;
use PhpBrew\Patches\IntlWith64bitPatch;
use PhpBrew\Utils;

class IntlWith64bitPatchTest extends PHPUnit_Framework_TestCase
{
    protected function cleanupBuildDirectory()
    {
        $sourceDirectory = getenv('PHPBREW_BUILD_PHP_DIR');
        if (!is_dir($sourceDirectory)) {
            return;
        }

        $directoryIterator = new RecursiveDirectoryIterator($sourceDirectory, RecursiveDirectoryIterator::SKIP_DOTS);
        $it = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        if (is_dir($sourceDirectory)) {
            rmdir($sourceDirectory);
        } elseif (is_file($sourceDirectory)) {
            unlink($sourceDirectory);
        }
    }

    public function setUp()
    {
        $sourceDirectory = getenv('PHPBREW_BUILD_PHP_DIR');
        $this->cleanupBuildDirectory();
        if (!file_exists($sourceDirectory)) {
            mkdir($sourceDirectory, 0755, true);
        }
    }

    public function tearDown()
    {
        $sourceDirectory = getenv('PHPBREW_BUILD_PHP_DIR');

        // don't clean up if the test failed.
        if ($this->hasFailed()) {
            return;
        }
        $this->cleanupBuildDirectory();
    }


    public function testPatch()
    {
        $logger = new Logger();
        $logger->setQuiet();

        $version = '5.3.29';
        $sourceFixtureDirectory = getenv('PHPBREW_FIXTURES_PHP_DIR') . DIRECTORY_SEPARATOR . $version;
        $sourceExpectedDirectory = getenv('PHPBREW_EXPECTED_PHP_DIR') . DIRECTORY_SEPARATOR . $version;
        $sourceDirectory = getenv('PHPBREW_BUILD_PHP_DIR');

        if (!is_dir($sourceDirectory)) {
            return $this->markTestSkipped("$sourceDirectory does not exist.");
        }

        // Copy the source Makefile to the Makefile
        copy($sourceFixtureDirectory . '/Makefile', $sourceDirectory . '/Makefile');

        $build = new Build($version);
        $build->setSourceDirectory($sourceDirectory);
        $build->enableVariant('intl');
        $this->assertTrue($build->hasVariant('intl'), 'intl enabled');

        $patch = new IntlWith64bitPatch;
        $matched = $patch->match($build);
        $this->assertTrue($matched, 'patch matched');
        $patchedCount = $patch->apply($build, $logger);
        $this->assertEquals(3, $patchedCount);



        $this->assertFileEquals($sourceExpectedDirectory. '/Makefile', $sourceDirectory . '/Makefile');


    }
}
