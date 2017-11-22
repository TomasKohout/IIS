<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:56
 */

namespace App\Model;

use Nette;


class CleanModel {
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function searchClean(array $values){
        return $this->database->table('cisteni')->where(array_filter($values));
    }

    public function allClean(){

        return $this->database->table('cisteni');
    }

    public function addClean(array $values)
    {
        $this->database->table('cisteni')->insert($values);
    }



}