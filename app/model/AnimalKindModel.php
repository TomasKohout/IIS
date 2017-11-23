<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 23.11.17
 * Time: 20:36
 */

namespace App\Model;
use Nette;

class AnimalKindModel
{
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }


    public function searchKind($getValues)
    {
        return $this->database->table('druh_zvirete')->where(array_filter($getValues));

    }

}