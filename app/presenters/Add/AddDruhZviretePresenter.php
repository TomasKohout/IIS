<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 23.10.17
 * Time: 12:15
 */

namespace App\Presenters;
use Nette;
use App\Model\AnimalModel;
use Nette\Utils\Html;

class AddDruhZviretePresenter extends BasePresenter
{
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault()
    {

    }

    public function createComponentAddDruhZvirete(){
        $model = new AnimalModel($this->database);
        $form = $this->form();
        $form->addSelect('naSkoleni', 'Vyber potřebné školení:', $model->getSkoleni())
            ->setRequired('Školení je požadovaná hodnota!');
        $form->addText('nazev', 'Název druhu:')
            ->setDefaultValue('Název')
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30)
            ->setRequired(true);
        $form->addText('vyskyt', 'Výskyt:')
            ->addRule($form::MAX_LENGTH,'Název je příliš dlouhý. Maximální délka je %d.',30)
            ->setRequired(true);
        $form->addSubmit('submit', 'Přidat druh');
        $form->onSuccess[] = [$this, 'addDruhZvireteSucceed'];
        return $form;
    }

    public function addDruhZvireteSucceed(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values)
    {
        $model = new AnimalModel($this->database);
        $model->addDruh($form->getValues(true));
        $this->flashMessage('Druh přidán!' ,'success');
        $this->redirect('AddDruhZvirete:');
    }
}