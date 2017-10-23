<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 18.10.17
 * Time: 21:50
 */

namespace App\Presenters;
use Nette;

class MainPagePresenter extends \Nette\Application\UI\Presenter
{
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault(){
        $this->template->data = $this->database->query('SELECT zvire.id_zvire,zvire.jmeno, krmeni.cas FROM zvire LEFT JOIN krmeni ON zvire.id_zvire = krmeni.jekrmeno ORDER BY zvire.id_zvire DESC');
    }
}