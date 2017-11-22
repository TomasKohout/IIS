<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 19.10.17
 * Time: 11:56
 */

namespace App\Model;

use Nette;


class FeedModel {
    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function searchFeed(array $values){
        return $this->database->table('krmeni')->where(array_filter($values));
    }

    public function allFeed(){

        return $this->database->table('krmeni');
    }

    public function addFeed(array $values)
    {
        $this->database->table('krmeni')->insert($values);
    }



}