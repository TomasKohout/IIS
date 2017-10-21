<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:51
 */

namespace App\Presenters;
use Nette;
use Nette\Application\UI\Form;

class AddAnimalPresenter extends Nette\Application\UI\Presenter
{

    /** @var \Instante\ExtendedFormMacros\IFormFactory @inject */
    public $formFactory;

    public function renderDefault()
    {

    }

    public function createComponentAddAnimal()
    {
        $sex = ['M' => 'muž', 'Z' => 'žena'];
        $form = $this->formFactory->create();
        $form->addText('jmeno', 'Jméno zvířete: ')
            ->setAttribute('class' ,'col-sm-2')
            ->setRequired();
        $form->addText('jeDruhu', 'Druh: ')
            ->setRequired();
        $form->addRadioList('pohlavi', 'Pohlaví:', $sex)
            ->setRequired();
        $form->addText('vaha', 'Váha:')
            ->setRequired();
        $form->addText('vyska', 'Výška:')
            ->setRequired();
        $form->addText('jmeno_matky', 'Jméno matky:')
            ->setRequired();
        $form->addText('jmeno_otce', 'Jméno otce:')
            ->setRequired();
        $form->addText('date', "Datum:")
            ->setRequired("Datum narození je povinný údaj")
            ->setAttribute("class", "dtpicker col-sm-2")
            ->setAttribute('placeholder', 'rrrr.mm.dd')
            ->addRule($form::PATTERN, "Datum musí být ve formátu YYYY.MM.DD", "(19|20)\d\d\.(0[1-9]|1[012])\.(0[1-9]|[12][0-9]|r[01])");

        $form->addSubmit('submit', 'Přidat');
        $form->onSuccess[] = [$this, 'addAnimalSucceed'];
        return $form;

    }

    protected function addAnimalSucced(Form $form, Nette\Utils\ArrayHash $values)
    {
                
    }
}