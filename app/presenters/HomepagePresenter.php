<?php

namespace App\Presenters;

use Nette;
use App\Model\UserManager;
use Nette\Application\UI\Form;



class HomepagePresenter extends Nette\Application\UI\Presenter
{
    protected $database;
    protected $model;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        $this->model = new UserManager($database);
    }

    protected function createComponentSignInForm()
    {
        $form = $this->form();
        $form->addText('user', 'Uživatelské jméno:')
             ->setRequired('Zadejte uživatelské jméno:');
        $form->addPassword('password', 'Uživatelské heslo:')
             ->setRequired('Zadejte uživatelské heslo:');
        $form->addSubmit('send', 'Přihlásit');
        $form->onSuccess[] = [$this, 'singInFormSucceeded'];
        return $form;
    }


    public function singInFormSucceeded(Form $form, Nette\Utils\ArrayHash $values)
    {
        try{
            $this->user->login($values->user, $values->password);
            $this->redirect('MainPage:');
        } catch (Nette\Security\AuthenticationException $exception)
        {
            $form->addError($exception->getMessage());
        }



    }

    public function renderChangeCredentials(){

    }

    public function createComponentChangeCredentials(){
        $form = $this->form();
        $form->addText('adresa', 'Adresa trvalého bydliště: ')
            ->setRequired('Pro změnu trvalého bydliště musíte vyplnit adresu trvalého bydliště.');

        $form->addSubmit('submit', 'Změnit adresu');

        $form->onSuccess[] = [$this, 'changeCredentialsSucceed'];

        return $form;
    }

    public function changeCredentialsSucceed(Form $form){
        $this->model->changeCredntials($form->getValues(true), $this->user->getId());
        $this->flashMessage('Adresa byla změněna.', 'success');
        $this->redirect('MainPage:default');
    }

    public function renderChangePass(){

    }

    public function createComponentChangePass(){
        $form = $this->form();

        $form->addPassword('heslo', 'Nové heslo: ')
            ->setRequired('Zadejte nové heslo.')
            ->addRule($form::MIN_LENGTH, 'Minimální délka je 6 znaků.', 6);
        $form->addPassword('hesloControl', 'Nové heslo podruhé: ')
            ->setRequired('Zadejte nové heslo podruhé!');
        $form->addSubmit('submit', 'Změnit heslo');
        $form->onSuccess[] = [$this, 'changePassSucceed'];
        return $form;
    }

    public function changePassSucceed(Form $form, Nette\Utils\ArrayHash $values){
        if (strcmp($values->heslo, $values->hesloControl) != 0) {
            $this->flashMessage('Hesla se neshodujíí!', 'danger');
            $this->redirect('Homepage:changePass');
        }

        $this->model->changePass($values->heslo, $this->user->getId());
        $this->flashMessage('Heslo bylo úspěšně změněno.', 'success');
        $this->redirect('MainPage:default');


    }

    public function renderLogout(){
        $this->user->logout();
        $this->redirect('Homepage:default');
    }

    public function renderDefault()
	{
	    $this->template->log_page = true;
	}

	protected function form(){
        $form = new \Nette\Application\UI\Form;
        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');
        $form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer());
        return $form;
    }
}
