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
 * Compta : les réglements du jour
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */


$debug='';
$template="comptaToday";

//select typeID du groupe reglement
$listeTypeID = new msData();
if ($listeTypeID = $listeTypeID->getDataTypesFromGroupe('reglement', ['id'])) {
    foreach ($listeTypeID as $v) {
        $tabliste[]=$v['id'];
    }
}

//liste praticiens autorisé
$pratIdAutorises[]=$p['user']['id'];
if (isset($p['config']['administratifComptaPeutVoirRecettesDe'])) {
    $pratIdAutorises=array_merge($pratIdAutorises, explode(',', $p['config']['administratifComptaPeutVoirRecettesDe']));
    $pratIdAutorises=array_unique($pratIdAutorises);
}
$p['page']['pratsAuto']=msSQL::sql2tabKey("select p.id, p.rank, o2.value as prenom, o.value as nom
 from people as p
 left join objets_data as o on o.toID=p.id and o.typeID=2 and o.outdated=''
 left join objets_data as o2 on o2.toID=p.id and o2.typeID=3 and o2.outdated=''
 where p.id in ('".implode("','", $pratIdAutorises)."') order by p.id", "id");

//sortir les reglements du jour
if ($lr=msSQL::sql2tab("select pd.toID, pd.fromID, pd.id, pd.typeID, pd.value, pd.creationDate, pd.instance, p.value as prenom , n.value as nom, a.label, dc.name
  from objets_data as pd
  left join data_types as dc on dc.id=pd.typeID
  left join actes as a on pd.parentTypeID=a.id
  left join objets_data as p on p.toID=pd.toID and p.typeID=3 and p.outdated=''
  left join objets_data as n on n.toID=pd.toID and n.typeID=2 and n.outdated=''
  where pd.typeId in (".implode(',', $tabliste).")  and DATE(pd.creationDate) = CURDATE() and pd.deleted='' and pd.fromID in ('".implode("','", array_keys($p['page']['pratsAuto']))."')
  order by pd.instance, pd.creationDate desc
  ")) {

    //constituer le tableau
    foreach ($lr as $v) {
        if ($v['instance']==0) {
            $tabReg[$v['id']]=$v;
        } else {
            $tabReg[$v['instance']][$v['name']]=$v['value'];
        }
    }

    //faire quelques calculs
    foreach ($tabReg as $k=>$v) {
        $tabReg[$k]['dejaPaye']=number_format($v['regleCheque']+$v['regleCB']+$v['regleEspeces']+$v['regleTiersPayeur'], 2, '.', '');
        $tabReg[$k]['resteAPaye']=number_format($v['regleFacture']-$tabReg[$k]['dejaPaye'], 2, '.', '');
    }

    //séparer en paiement complété et paiement à faire
    foreach ($tabReg as $k=>$v) {
        if ($tabReg[$k]['dejaPaye'] != $tabReg[$k]['regleFacture']) {
            $p['page']['tabRegNC'][$k]=$tabReg[$k];
        } else {
            $p['page']['tabRegC'][$k]=$tabReg[$k];
        }
    }
}
