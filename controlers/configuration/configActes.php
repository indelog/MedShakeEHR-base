<?php
/*
 * This file is part of MedShakeEHR.
 *
 * Copyright (c) 2017
 * Bertrand Boutillier <b.boutillier@gmail.com>
 * http://www.medshake.net
 *
 * MedShakeEHR is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * MedShakeEHR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MedShakeEHR.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Config : gérer les actes
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

 //admin uniquement
 if (!msUser::checkUserIsAdmin()) {
     $template="forbidden";
 } else {
     $template="configActes";
     $debug='';

     //utilisateurs différents
     $autoUsers= new msPeople();
     $p['page']['users']=$autoUsers->getUsersListForService('administratifPeutAvoirFacturesTypes');
     if(is_array($p['page']['users'])) $p['page']['users']=array('0'=>'Tous')+$p['page']['users']; else {$p['page']['users']=array('0'=>'Tous');}


     // si user
     if (isset($match['params']['user'])) {
         $p['page']['selectUser']=$match['params']['user'];
         if (is_numeric($p['page']['selectUser'])) {
             $where[]="a.toID='".$p['page']['selectUser']."'";
         }

     } else {
         $where[]="a.toID='0'";
         $p['page']['selectUser']=0;
     }


     // si catégorie
     if (isset($match['params']['cat'])) {
         $cat=$match['params']['cat'];
         if (is_numeric($cat)) {
             $where[]="a.cat='".$cat."'";
         }
     }

     if ($tabTypes=msSQL::sql2tab("select a.* , c.name as catName, c.label as catLabel
					from actes as a
					left join actes_cat as c on c.id=a.cat
          where ".implode(' and ', $where)."
					group by a.id
					order by c.displayOrder, c.label asc, a.label asc")) {
         foreach ($tabTypes as $v) {
             $reglement = new msReglement();
             $reglement->set_secteurTarifaire($p['config']['administratifSecteurHonoraires']);
             $reglement->set_factureTypeID($v['id']);
             $reglement->set_factureTypeData($v);
             $p['page']['tabTypes'][$v['catName']][]=$reglement->getCalculateFactureTypeData();
         }
     }

     // liste des catégories
     $p['page']['catList']=msSQL::sql2tabKey("select id, label from actes_cat order by label", 'id', 'label');

     //utilisation de chaque facture type
     $typeID= msData::getTypeIDFromName('reglePorteur');
     $p['page']['utilisationParFacture']=msSQL::sql2tabKey("SELECT count(id) as nb, parentTypeID FROM objets_data WHERE typeID = $typeID group by parentTypeID", 'parentTypeID', 'nb');

 }
