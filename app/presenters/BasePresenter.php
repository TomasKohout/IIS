<?php

namespace App\Presenters;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    protected function form(){
        $form = new \Nette\Application\UI\Form;
        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');
        $form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer());
        return $form;
    }
}
