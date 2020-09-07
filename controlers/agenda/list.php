<?php
/*
 * This file is part of MedShakeEHR.
 *
 * Copyright (c) 2020
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
 * Prépare une liste de rendez-vous sur un interval de date données
 *
 * @author 2020     DEMAREST Maxime <maxime@indelog.fr>
 */

$debug='';
$template='agenda_list';

$pratID='';
$startDate='';
$endDate='';
$list_rdv = array();

// sanity check
$pratID=filter_input(INPUT_POST, 'pratID', FILTER_SANITIZE_NUMBER_INT);
$startDate=filter_input(INPUT_POST, 'startDate', FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=> "/\d{4}-\d{2}-\d{2}/")));
$endDate=filter_input(INPUT_POST, 'endDate', FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=> "/\d{4}-\d{2}-\d{2}/")));

// Si les params sonts fournis recherche l'agenda
if (!empty($pratID) and !empty($startDate) and !empty($endDate)) {

	$msAgenda = new msAgenda();
	$msAgenda->set_userID($pratID);
	$msAgenda->setStartDate($startDate);
	$msAgenda->setEndDate($endDate);
	$type_rdvs = $msAgenda->getRdvTypes();
	$rdvs = $msAgenda->getEvents();
	foreach($rdvs as $r) {
		$dateTime = new DateTime($r['start']);
		$day = $dateTime->format('d/m/Y');
		// Ignore fermée
		if (empty($type_rdvs[$r['type']]['descriptif'])) continue;
		if (empty($list_rdv[$day])) $list_rdv[$day] = array();
		$list_rdv[$day][] = array(
			'h_start'=>$dateTime->format('h:m'),
			'duree'=>$type_rdvs[$r['type']]['duree'],
			'consult'=>$type_rdvs[$r['type']]['descriptif'],
			'color'=>$type_rdvs[$r['type']]['backgroundColor'],
			'motif'=>$r['motif'],
			'title'=>$r['title'],
		);
	}
}

$p['page']['search']['pratID'] = $pratID;
$p['page']['search']['startDate'] = $startDate;
$p['page']['search']['endDate'] = $endDate;
$p['page']['list_rdv'] = $list_rdv;
