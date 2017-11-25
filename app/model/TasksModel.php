<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 23.11.17
 * Time: 17:47
 */

namespace App\Model;
use Nette;

class TasksModel
{

    protected $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }
    public function isValid($id_cisteni, $table){
        if(!$this->database->table($table)->get($id_cisteni))
            throw new Nette\Application\BadRequestException("Takové čištění pro daného uživatele neexistuje", 404);
    }

    public function tasksClean($rodne_cislo){
        $results = $this->database->table('provadi_cisteni')->where(['rd_osetrovatel' => $rodne_cislo, 'provedl' => '0']);
        $cisteni = $this->database->table('cisteni');
        $vybeh   = $this->database->table('vybeh');
        $typVybehu   = $this->database->table('typ_vybehu');

        $ret_array = array();
        $i = 0;
        foreach ($results as $row){
            $cisteniRow = $cisteni->get($row->id_cisteni);
            $vybehRow = $vybeh->get($cisteniRow->jeCisten);
            $typVybehuRow = $typVybehu->get($vybehRow->naTypVybehu);

            $ret_array[$i] = array();
            $ret_array[$i]['id_vybeh'] = array();
            $ret_array[$i]['id_vybeh'] = $vybehRow->id_vybeh;
            $ret_array[$i]['poloha']   = array();
            $ret_array[$i]['poloha']   = $vybehRow->poloha;
            $ret_array[$i]['id_cisteni'] = array();
            $ret_array[$i]['id_cisteni'] = $row->id_cisteni;
            $ret_array[$i]['pomucka_k_cisteni'] = array();
            $ret_array[$i]['pomucka_k_cisteni'] = $typVybehuRow->pomucka_k_cisteni;
            $ret_array[$i]['datum']      = array();
            $ret_array[$i]['datum']      = $cisteniRow->datum;
            $ret_array[$i]['id']         = array();
            $ret_array[$i]['id']         = $row->id;
            //dump($row->id);

            $i++;
        }

        return $ret_array;
    }

    public function tasksFeed($rodne_cislo){
        $results = $this->database->table('provadi_krmeni')->where(['rd_osetrovatel' => $rodne_cislo, 'provedl' => '0']);
        $krmeni  = $this->database->table('krmeni');
        $vybeh     = $this->database->table('vybeh');
        $zvire   = $this->database->table('zvire');

        $ret_array = array();
        $i = 0;

        foreach ($results as $row){
            $krmeniRow = $krmeni->get($row->id_krmeni);
            $zvireRow  = $zvire->get($krmeniRow->jeKrmeno);
            $vybehRow  = $vybeh->get($zvireRow->obyva);
            $ret_array[$i]  =   array();
            $ret_array[$i]['jmeno'] =   array();
            $ret_array[$i]['jmeno'] =   $zvireRow->jmeno;
            $ret_array[$i]['poloha'] = array();
            $ret_array[$i]['poloha'] = $vybehRow->poloha;
            $ret_array[$i]['id_vybeh'] = array();
            $ret_array[$i]['id_vybeh'] = $vybehRow->id_vybeh;
            $ret_array[$i]['datum'] = array();
            $ret_array[$i]['datum'] = $krmeniRow->datum;
            $ret_array[$i]['druh'] = array();
            $ret_array[$i]['druh'] = $krmeniRow->druh;
            $ret_array[$i]['mnozstvi'] = array();
            $ret_array[$i]['mnozstvi'] = $krmeniRow->mnozstvi;
            $ret_array[$i]['id'] = array();
            $ret_array[$i]['id'] = $row->id;
            $i++;

        }

        return $ret_array;
    }

    public function taskCleanDone($id){
        $this->database->table('provadi_cisteni')->where( 'id' , $id)->update(['provedl'=> '1']);
    }

    public function taskFeedDone($id){
        $this->database->table('provadi_krmeni')->where('id', $id)->update(['provedl'=> '1']);
    }



}