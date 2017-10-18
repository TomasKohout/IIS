<?php

namespace Instante\ExtendedFormMacros\DI;

use Instante\ExtendedFormMacros\Latte\FormMacros;
use Instante\ExtendedFormMacros\Latte\FormRenderingDispatcher;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

class ExtendedFormMacrosExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $FormMacros = FormMacros::class;
        $builder->addDefinition($this->prefix('formRenderingDispatcher'))
            ->setClass(FormRenderingDispatcher::class);
        $builder->getDefinition('latte.latteFactory')
            ->addSetup("?->onCompile[] = function() use (?) { $FormMacros::install(?->getCompiler()); }",
                ['@self', '@self', '@self',])
            ->addSetup("?->addProvider('formRenderingDispatcher', ?)", [
                '@self',
                $this->prefix('@formRenderingDispatcher'),
            ]);
    }

    public static function register(Configurator $config)
    {
        $config->onCompile[] = function (Configurator $config, Compiler $compiler) {
            $compiler->addExtension('extendedFormMacros', new self());
        };
    }

}
