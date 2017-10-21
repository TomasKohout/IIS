<?php

namespace App\Presenters;

use Nette;
use App\Model\UserManager;
use Nette\Application\UI\Form;



class HomepagePresenter extends \Nette\Application\UI\Presenter
{
    /** @var \Instante\ExtendedFormMacros\IFormFactory @inject */
    public $formFactory;
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    protected function createComponentSignInForm()
    {
        $form = $this->formFactory->create();
        $form->addText('user', 'Uživatelské jméno:')
             ->setRequired('Zadejte uživatelské jméno:');
        $form->addPassword('password', 'Uživatelské heslo:')
             ->setRequired('Zadejte uživatelské heslo:');
        $form->addSubmit('send', 'Přihlásit');
        $form->getRenderer()->setLabelColumns(4,4);
        $form->onSuccess[] = [$this, 'singInFormSucceeded'];
        return $form;
    }


    public function singInFormSucceeded(Form $form, Nette\Utils\ArrayHash $values)
    {
        try{
            $authenticator = new UserManager($this->database);
            $authenticator->authenticate(array($values->user, $values->password));
            $this->redirect('MainPage:');
        } catch (Nette\Security\AuthenticationException $exception)
        {
            $form->addError($exception->getMessage());
        }



    }

    public function renderDefault()
	{
	    $this->template->log_page = true;
	}
}
