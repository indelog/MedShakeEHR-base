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
 * Js pour le module creation patient / praticien
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 * @edited fr33z00 <https://www.github.com/fr33z00>
 */

$(document).ready(function() {

  //autocomplete pour la liaison code postal - > ville
  $('body').delegate('#p_13ID, #p_53ID', 'focusin', function() {
    type = $(this).attr('data-typeID');
    if (type == 53) dest = 56;
    else if (type == 13) dest = 12;

    if ($(this).is(':data(autocomplete)')) return;
    $(this).autocomplete({
      source: '/ajax/getAutocompleteLinkType/data_types/' + type + '/' + type + '/' + type + ':' + dest + '/',
      autoFocus: true,
      minLength: 3,
      select: function(event, ui) {
        destival = eval('ui.item.d' + dest);
        $('#p_' + dest + 'ID').val(destival);
      }
    });
  });


  //réactiver un dossier marqué comme supprimé
  $('body').on("click", "a.unmarkDeleted", function(e) {
    e.preventDefault();
    if (confirm("Ce dossier sera à nouveau visible dans les listings de recherche.\nSouhaitez-vous poursuivre ? ")) {
      source = $(this);
      $.ajax({
        url: urlBase+'/patients/ajax/unmarkDeleted/',
        type: 'post',
        data: {
          patientID: $(this).attr('data-patientID'),
        },
        dataType: "html",
        success: function(data) {
          el = source.closest('tr');
          el.css("background", "#efffe8");
          setTimeout(function() {
            el.css("background", "");
            el.remove();
          }, 1000);

        },
        error: function() {
          alert('Problème, rechargez la page !');
        }
      });

    }
  });


});
