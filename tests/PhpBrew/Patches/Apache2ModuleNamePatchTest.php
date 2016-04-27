<?php
use CLIFramework\Logger;
use PhpBrew\Build;
use PhpBrew\Patches\IntlWith64bitPatch;
use PhpBrew\Patches\Apache2ModuleNamePatch;
use PhpBrew\Utils;
use PhpBrew\Testing\PatchTestCase;

/**
 * @small
 */
class Apache2ModuleNamePatchTest extends PatchTestCase
{
    public function testPatch()
    {
        $logger = new Logger();
        $logger->setQuiet();

        $version = '5.5.17';
        $sourceFixtureDirectory = getenv('PHPBREW_FIXTURES_PHP_DIR') . DIRECTORY_SEPARATOR . $version;
        $sourceExpectedDirectory = getenv('PHPBREW_EXPECTED_PHP_DIR') . DIRECTORY_SEPARATOR . $version;
        $sourceDirectory = getenv('PHPBREW_BUILD_PHP_DIR');

        if (!is_dir($sourceDirectory)) {
            return $this->markTestSkipped("$sourceDirectory does not exist.");
        }

        $this->setupBuildDirectory($version);

        $build = new Build($version);
        $build->setSourceDirectory($sourceDirectory);
        $build->enableVariant('apxs2');
        $this->assertTrue($build->hasVariant('apxs2'), 'apxs2 enabled');

        $patch = new Apache2ModuleNamePatch;
        $matched = $patch->match($build, $logger);
        $this->assertTrue($matched, 'patch matched');
        $patchedCount = $patch->apply($build, $logger);
        $this->assertEquals(107, $patchedCount);

        $this->assertFileEquals($sourceExpectedDirectory. '/Makefile.global', $sourceDirectory . '/Makefile.global');
        $this->assertFileEquals($sourceExpectedDirectory. '/configure', $sourceDirectory . '/configure');
    }
}
