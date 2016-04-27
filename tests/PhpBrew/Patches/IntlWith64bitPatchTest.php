<?php
use CLIFramework\Logger;
use PhpBrew\Build;
use PhpBrew\Patches\IntlWith64bitPatch;
use PhpBrew\Utils;
use PhpBrew\Testing\PatchTestCase;

class IntlWith64bitPatchTest extends PatchTestCase
{
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
        $matched = $patch->match($build, $logger);
        $this->assertTrue($matched, 'patch matched');
        $patchedCount = $patch->apply($build, $logger);
        $this->assertEquals(3, $patchedCount);



        $this->assertFileEquals($sourceExpectedDirectory. '/Makefile', $sourceDirectory . '/Makefile');


    }
}
