<?php
namespace PhpBrew\Patches;
use PhpBrew\Buildable;
use PhpBrew\PatchKit\Patch;
use PhpBrew\PatchKit\RegExpPatchRule;
use CLIFramework\Logger;

class Apache2ModuleNamePatch extends Patch
{
    public function desc()
    {
        return "replace apache php module name with custom version name";
    }

    public function match(Buildable $build, Logger $logger)
    {
        return $build->hasVariant('apxs2');
    }

    public function rules()
    {
        $rules = [];
        $rules[] = RegExpPatchRule::files(array('configure', 'Makefile.global'))
            ->always()
            ->replaces(
                '#libphp\$\(PHP_MAJOR_VERSION\)#',
                'libphp$(PHP_VERSION)');

        $rules[] = RegExpPatchRule::files(array('configure', 'Makefile.global'))
            ->always()
            ->replaces(
                '#libphp\$PHP_MAJOR_VERSION#',
                'libphp$PHP_VERSION');

        $rules[] = RegExpPatchRule::files(array('configure', 'Makefile.global'))
            ->always()
            ->replaces(
                '#libs/libphp[57].(so|la)#',
                'libs/libphp\$PHP_VERSION.$1');


        return $rules;
    }
}





