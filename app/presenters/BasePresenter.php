<?php

namespace App\Presenters;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    protected function startup(){
        parent::startup();
        $this->user->setExpiration('30 minutes');
        if (!$this->user->isLoggedIn())
        {
            $this->redirect("Homepage:default");
        }


        $this->template->user = $this->getUser();


    }
    protected function removeEmpty(array $array){

        foreach ($array as $key => $value){
            if ($array[$key] == null || $array[$key] === '0')
                unset($array[$key]);

        }
        return $array;

    }
    public function checkRequirements($element){

    }

    protected function form(){
        $form = new \Nette\Application\UI\Form;
        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');
        $form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer());
        return $form;
    }

    protected function getCountries(){

         $countries = array('AFGHANISTÁN', 'ALBÁNIE', 'ALŽÍR', 'AMERISKÁ SAMOA', 'ANGOLA', 'ANGUILLA', 'ANTIGUA A BARBUDA', 'ARGENTINA', 'ARMENIE', 'ARUBA', 'AUSTRÁLIE', 'AZERBÁJDŽÁN', 'BAHAMY', 'BAHRAJN', 'BANGLADÉŠ', 'BARBADOS', 'BELGIE', 'BELIZE', 'BĚLORUSKO', 'BENIN', 'BHUTAN', 'BOLÍVIE', 'BOSNA A HERCEGOVINA', 'BOTSWANA', 'BRAZÍLIE', 'BRITSKÉ PANENSKÉ OSTROVY', 'BRUNEI DARUSSALAM', 'BULHARSKO', 'BURKINA FASO', 'BURUNDI', 'COMOROS', 'COOKOVY OSTROVY', 'CURACAO', 'ČAD', 'ČESKÁ REPUBLIKA', 'ČILE', 'ČÍNA', 'DÁNSKO', 'DOMINIKA', 'DOMINIKÁNSKÁ REP.', 'DŽIBUTSKO', 'EGYPT', 'EKVÁDOR', 'ERITREA', 'ESTONSKO', 'ETIOPIE', 'FALKLANDSKÉ OSTROVY', 'FIDŽI', 'FILIPÍNY', 'FINSKO', 'FRANCIE', 'FRANCOUZSKÁ GUIANA', 'FRANCOUZSKÁ POLYNÉZIE', 'GABON', 'GAMBIE', 'GHANA', 'GIBRALTAR', 'GRENADA', 'GRUZIE', 'GUADELOUPE', 'GUAM', 'GUATEMALA', 'GUINEA', 'GUINEA BISSAU', 'GUYANA', 'HAITI', 'HONDURAS', 'HONG KONG', 'CHORVATSKO', 'INDIE', 'INDONÉZIE', 'IRÁK', 'IRSKO', 'ISLAND', 'ITÁLIE', 'IZRAEL', 'JAMAICA', 'JAPONSKO', 'JEMEN', 'JIŽNÍ KOREA', 'JORDÁNSKO', 'KAJMANSKÉ OSTROVY', 'KAMBODŽA', 'KAMERUN', 'KANADA', 'KAPVERDY', 'KATAR', 'KAZACHSTÁN', 'KEŇA', 'KIRIBATI', 'KOLUMBIE', 'KONGO', 'KONGO DEM. REP.', 'KOSOVO', 'KOSTARIKA', 'KUWAIT', 'KYPR', 'KYPR (SEVERNÍ)', 'KYRGYZSTÁN', 'LAOS', 'LIBANON', 'LIBÉRIE', 'LIBYE', 'LICHTENŠTEJNSKO', 'LITVA', 'LOTYŠSKO', 'LUCEMBURSKO', 'MACAU', 'MADAGASKAR', 'MAĎARSKO', 'MAKEDONIE', 'MALAJSIE', 'MALAWI', 'MALEDIVY', 'MALI', 'MALTA', 'MAROKO', 'MARŠALOVY OSTROVY', 'MARTINIK', 'MAURETÁNIE', 'MAURÍCIUS', 'MEXIKO', 'MOLDÁVIE', 'MONAKO', 'MONGOLSKO', 'MONTSERRAT', 'MOZAMBIK', 'NĚMECKO', 'NEPÁL', 'NIGER', 'NIGÉRIE', 'NIKARAGUA', 'NIUE', 'NIZOZEMÍ', 'NORSKO', 'NOVÁ KALEDONIE', 'NOVÝ ZÉLAND', 'OMÁN', 'PÁKISTÁN', 'PALESTINSKÁ ÚZEMÍ', 'PANAMA', 'PANENSKÉ OSTROVY', 'PAPUA NOVÁ GUINEA', 'PARAGUAY', 'PERU', 'POBŘEŽÍ SLONOVINY', 'POLSKO', 'PORTORIKO', 'PORTUGALSKO', 'RAKOUSKO', 'RÉUNION', 'ROVNÍKOVÁ GUINEA', 'RUMUNSKO', 'RUSKO', 'RWANDA', 'ŘECKO', 'S. KITTS A NEVIS', 'S. LUCIE', 'S. MAARTEN', 'S. MARTIN', 'S. TOMÁŠ', 'S. VINCENT', 'SALVADOR', 'SAMOA', 'SAUDSKÁ ARÁBIE', 'SENEGAL', 'SEVERNÍ MARIÁNSKÉ OSTROVY', 'SIERRA LEONE', 'SINGAPUR', 'SLOVENSKO', 'SLOVINSKO', 'SPOJENÉ ARABSKÉ EMIRÁTY', 'SPOJENÉ STÁTY AMERICKÉ', 'SRBSKO A ČERNÁ HORA', 'SRÍ LANKA', 'STŘEDOAFRICKÁ REPUBLIKA', 'SUDAN', 'SURINAM', 'SÝRIE', 'ŠALAMOUNOVY OSTR.', 'ŠPANĚLSKO', 'ŠVÉDSKO', 'ŠVÝCARSKO', 'TADŽIKISTÁN', 'TAIWAN', 'TANZÁNIE', 'THAJSKO', 'TOGO', 'TONGA', 'TRINIDAD A TOBAGO', 'TUNISKO', 'TURECKO', 'TURKMENISTAN', 'TURKS A CICOS', 'TUVALU', 'UGANDA', 'UKRAJINA', 'URUGUAY', 'UZBEKISTÁN', 'VANUATU', 'VELKÁ BRITÁNIE', 'VENEZUELA', 'VIETNAM', 'VÝCHODNÍ TIMOR', 'ZAMBIE', 'ZIMBABWE');
         $ret = array();

         foreach ($countries as $country)
         {
             $temp = ucwords(mb_strtolower($country));
             $ret[$temp] = array();
             $ret[$temp] = $temp;
         }

         return $ret;

    }

    public function arrayHasDupes($array){
        return count(array_map("serialize",$array)) != count(array_unique(array_map("serialize",$array)));
    }
}
