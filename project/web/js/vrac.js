/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var initConditions = function()
{
    if ($('#vrac_condition #type_contrat input:checked').length == 0)
        $('#vrac_condition #type_contrat input[value="SPOT"]').attr('checked', 'checked');
    if ($('#vrac_condition #prix_isVariable input:checked').length == 0)
        $('#vrac_condition #prix_isVariable input[value="0"]').attr('checked', 'checked');
    updatePanelsConditions();
    $('#vrac_condition #type_contrat input').click(updatePanelsConditions);
    $('#vrac_condition #prix_isVariable input').click(updatePanelsConditions);
    $('#vrac_condition input#vrac_enlevement_date').click(function(){
        $(this).datepicker('show');       
    });
};


var updatePanelsConditions = function()
{
    if ($('#vrac_condition #type_contrat input:checked').val() == 'SPOT')
    {
        $('#prix_isVariable').hide();
    }
    else
    {
        $('#prix_isVariable').show();
        if ($('#vrac_condition #prix_isVariable input:checked').val() == '0')
        {
            $('#prix_variable').hide();
        }
        else
        {
            $('#prix_variable').show();

        }
    }
};

var initMarche = function(isTeledeclarationMode)
{
 
    if ($('#vrac_marche #original input:checked').length == 0)
        $('#vrac_marche #original input[value="1"]').attr('checked', 'checked');
    if ($('#vrac_marche #type_transaction input:checked').length == 0)
        $('#vrac_marche #type_transaction input[value="VIN_VRAC"]').attr('checked', 'checked');
    if ($('#type input[name="vrac[categorie_vin]"]:checked').length == 0)
        $('#type input[value="GENERIQUE"]').attr('checked', 'checked');

    if ($('#type input[value="GENERIQUE"]:checked').length > 0) {
        $('#domaine').hide();
    }



    $('#type input').click(function()
    {
        if ($(this).val() == 'GENERIQUE')
            $('#domaine').hide();
        else
            $('#domaine').show();
    });
    
    updatePanelsAndUnitLabels(isTeledeclarationMode);
    $('#vrac_marche #type_transaction input').click(function() {
        clearVolumesChamps();
        updatePanelsAndUnitLabels(isTeledeclarationMode);
    });
};

var isTeledeclarationMode = false;

var updatePanelsAndUnitLabels = function(isTeledeclarationMode)
{
    this.isTeledeclarationMode = isTeledeclarationMode;
    switch ($('#vrac_marche #type_transaction input:checked').attr('value'))
    {
        case 'RAISINS' :
            {
                updatePanelsAndUnitForRaisins(isTeledeclarationMode);

                $('#vrac_raisin_quantite').unbind();
                $('#vrac_prix_initial_unitaire').unbind();
                $('#vrac_prix_unitaire').unbind();

                $('#vrac_raisin_quantite').bind('keyup', updatePanelsAndUnitForRaisins);
                $('#vrac_prix_initial_unitaire').bind('keyup', updatePanelsAndUnitForRaisins);
                $('#vrac_prix_unitaire').bind('keyup', updatePanelsAndUnitForRaisins);
                $('#vrac_raisin_quantite').bind('click', updatePanelsAndUnitForRaisins);
                $('#vrac_prix_initial_unitaire').bind('click', updatePanelsAndUnitForRaisins);
                $('#vrac_prix_unitaire').bind('click', updatePanelsAndUnitForRaisins);
            }
            break;
        case 'MOUTS' :
            {
                updatePanelsAndUnitForJuice(isTeledeclarationMode);

                $('#vrac_jus_quantite').unbind();
                $('#vrac_prix_initial_unitaire').unbind();

                $('#vrac_jus_quantite').bind('keyup', updatePanelsAndUnitForJuice);
                $('#vrac_prix_initial_unitaire').bind('keyup', updatePanelsAndUnitForJuice);
                $('#vrac_prix_unitaire').bind('keyup', updatePanelsAndUnitForJuice);
                $('#vrac_jus_quantite').bind('click', updatePanelsAndUnitForJuice);
                $('#vrac_prix_initial_unitaire').bind('click', updatePanelsAndUnitForJuice);
                $('#vrac_prix_unitaire').bind('click', updatePanelsAndUnitForJuice);
            }
            break;
        case 'VIN_VRAC' :
            {
                updatePanelsAndUnitForJuice(isTeledeclarationMode);

                $('#vrac_jus_quantite').unbind();
                $('#vrac_prix_initial_unitaire').unbind();

                $('#vrac_jus_quantite').bind('keyup', updatePanelsAndUnitForJuice);
                $('#vrac_prix_initial_unitaire').bind('keyup', updatePanelsAndUnitForJuice);
                $('#vrac_prix_unitaire').bind('keyup', updatePanelsAndUnitForJuice);
                $('#vrac_jus_quantite').bind('click', updatePanelsAndUnitForJuice);
                $('#vrac_prix_initial_unitaire').bind('click', updatePanelsAndUnitForJuice);
                $('#vrac_prix_unitaire').bind('click', updatePanelsAndUnitForJuice);
            }
            break;
        case 'VIN_BOUTEILLE' :
            {
                updatePanelsAndUnitForBottle(isTeledeclarationMode);

                $('#vrac_bouteilles_quantite').unbind();
                $('#vrac_prix_initial_unitaire').unbind();
                $('#vrac_bouteilles_contenance_libelle').unbind();

                $('#vrac_bouteilles_quantite').bind('keyup', updatePanelsAndUnitForBottle);
                $('#vrac_prix_initial_unitaire').bind('keyup', updatePanelsAndUnitForBottle);
                $('#vrac_prix_unitaire').bind('keyup', updatePanelsAndUnitForBottle);
                $('#vrac_bouteilles_quantite').bind('click', updatePanelsAndUnitForBottle);
                $('#vrac_prix_initial_unitaire').bind('click', updatePanelsAndUnitForBottle);
                $('#vrac_prix_unitaire').bind('click', updatePanelsAndUnitForBottle);
                $('#vrac_bouteilles_contenance_libelle').bind('change', updatePanelsAndUnitForBottle);
            }
            break;
    }
};

var updatePanelsAndUnitForRaisins = function(isTeledeclarationMode)
{
    $('.bouteilles_contenance_libelle').hide();
    $('.bouteilles_quantite').hide();
    $('.jus_quantite').hide();
    $('.raisin_quantite').show();
    $('.raisin_quantite span#volume_unite_total').text("(en kg)");
    $('#prixInitialUnitaire span#prix_initial_unitaire_unite').text("€/kg");
    $('#prixUnitaire span#prix_unitaire_unite').text("€/kg");

    majTotal("raisin_quantite",isTeledeclarationMode);
};

var updatePanelsAndUnitForJuice = function(isTeledeclarationMode)
{
    $('.bouteilles_contenance_libelle').hide();
    $('.bouteilles_quantite').hide();
    $('.raisin_quantite').hide();
    $('.jus_quantite').show();
    $('.jus_quantite span#volume_unite_total').text("(en hl)");
    $('#prixInitialUnitaire span#prix_initial_unitaire_unite').text("€/hl");
    $('#prixUnitaire span#prix_unitaire_unite').text("€/hl");

    majTotal("jus_quantite",isTeledeclarationMode);
};

var updatePanelsAndUnitForBottle = function(isTeledeclarationMode)
{
    $('.raisin_quantite').hide();
    $('.jus_quantite').hide();

    $('.bouteilles_contenance_libelle').show();
    $('.bouteilles_quantite').show();

    var volume_total = 0.0;
    var bouteilles_quantite = $('#vrac_bouteilles_quantite').val();
    var bouteilles_contenance_libelle = $('#vrac_bouteilles_contenance_libelle').val();
    var bouteilles_contenance = getBouteilleContenance(bouteilles_contenance_libelle);
    var unitBouteilleOuBib = (bouteilles_contenance_libelle.contains("BIB")) ? 'en BIB' : 'en bouteilles';
    var prixParUnitBouteilleOuBib = (bouteilles_contenance_libelle.contains("BIB")) ? '€/BIB' : '€/btlle';

    $('.bouteilles_quantite span#volume_unite_total').text('(' + unitBouteilleOuBib + ')');
    $('#prixInitialUnitaire span#prix_initial_unitaire_unite').text(prixParUnitBouteilleOuBib);

    if (bouteilles_quantite === "" || bouteilles_contenance === "")
        return;

    var numeric = new RegExp("^[0-9]*$", "g");

    if (numeric.test(bouteilles_quantite))
    {
        volume_total = bouteilles_contenance * parseInt(bouteilles_quantite);
        volume_total = parseFloat(volume_total);
        $('#volume_total').val(volume_total).trigger('change');

        var unit = '';
        if(this.isTeledeclarationMode){
            unit = '(' + unitBouteilleOuBib + ')' ;
        }else{
            unit =  "(" + unitBouteilleOuBib + " soit " + volume_total.toFixed(2) + " hl)";
        }
        $('.bouteilles_quantite span#volume_unite_total').text(unit);
        var bouteilles_price_initial = $('#vrac_prix_initial_unitaire').val();
        var bouteilles_price = $('#vrac_prix_unitaire').val();
        var bouteilles_price_initialReg = (new RegExp("^[0-9]+[.]?[0-9]*$", "g")).test(bouteilles_price_initial);
        var bouteilles_price_Reg = (new RegExp("^[0-9]+[.]?[0-9]*$", "g")).test(bouteilles_price);
        if (bouteilles_price_initialReg)
        {
            var prix_initial_total = parseInt(bouteilles_quantite) * parseFloat(bouteilles_price_initial);
            $('#vrac_prix_initial_total').text(parseFloat(prix_initial_total).toFixed(2));
            $('#vrac_prix_initial_unite').html('€');
            var prix_initial_hl = prix_initial_total / volume_total;
            $('#prixInitialUnitaire span#prix_initial_unitaire_unite').text(prixParUnitBouteilleOuBib);
            if (!this.isTeledeclarationMode) {
                $('#prixInitialUnitaire span#prix_initial_unitaire_hl').text("(soit " + parseFloat(prix_initial_hl).toFixed(2) + " €/hl)");
            }
        }
        if (bouteilles_price_Reg)
        {
            var prix_total = parseInt(bouteilles_quantite) * parseFloat(bouteilles_price);
            $('#vrac_prix_total').text(parseFloat(prix_total).toFixed(2));
            $('#vrac_prix_unite').html('€');
            var prix_hl = prix_total / volume_total;
            $('#prixUnitaire span#prix_unitaire_unite').text("€/btlle");
            if (!this.isTeledeclarationMode) {
                $('#prixUnitaire span#prix_unitaire_hl').text("(soit " + parseFloat(prix_hl).toFixed(2) + " €/hl)");
            }
        }
    }
};

var majTotal = function(quantiteField) {
    var quantite = $('#vrac_' + quantiteField).val();
    var numericComma = new RegExp("^[0-9]+\,?[0-9]*$", "g");
    if (numericComma.test(quantite))
    {
        quantite = quantite.replace(",", ".");
    }

    var prix_initial_unitaire = $('#vrac_prix_initial_unitaire').val();
    var prix_unitaire = $('#vrac_prix_unitaire').val();
    if (numericComma.test(prix_initial_unitaire))
    {
        prix_initial_unitaire = prix_initial_unitaire.replace(",", ".");
    }
    if (numericComma.test(prix_unitaire))
    {
        prix_unitaire = prix_unitaire.replace(",", ".");
    }
    var numeric = new RegExp("^[0-9]+\.?[0-9]*$", "g");

    if (numeric.test(quantite))
    {
        var hlRaisins = quantite;
        if (quantiteField == 'raisin_quantite')
        {
            hlRaisins = (hlRaisins / densites[$('#vrac_produit').val()]);
            hlRaisins = hlRaisins / 100.0;
        }


        var priceInitialReg = (new RegExp("^[0-9]+[.]?[0-9]*$", "g")).test(prix_initial_unitaire);
        var priceReg = (new RegExp("^[0-9]+[.]?[0-9]*$", "g")).test(prix_unitaire);
        if (priceInitialReg)
        {
            var prix_initial_total = quantite * parseFloat(prix_initial_unitaire);
            var prix_total = quantite * parseFloat(prix_unitaire);
            $('#vrac_prix_initial_total').text(parseFloat(prix_initial_total).toFixed(2));
            $('#vrac_prix_initial_unite').text('€');
            if (quantiteField == 'raisin_quantite' && !this.isTeledeclarationMode)
            {
                var prix_initial_hl = prix_initial_total / hlRaisins;
                $('#prixInitialUnitaire span#prix_initial_unitaire_unite').text("€/kg (soit " + parseFloat(prix_initial_hl).toFixed(2) + " €/hl)");
            }
        }
        if (priceReg)
        {
            var prix_total = quantite * parseFloat(prix_unitaire);
            $('#vrac_prix_total').text(parseFloat(prix_total).toFixed(2));
            $('#vrac_prix_unite').text('€');

            if (quantiteField == 'raisin_quantite' && !this.isTeledeclarationMode)
            {
                var prix_hl = prix_initial_total / hlRaisins;
                $('#prixInitialUnitaire span#prix_unitaire_unite').text("€/kg (soit " + parseFloat(prix_hl).toFixed(2) + " €/hl)");
            }
        }
    }
};

var changeMillesimeLabelAndDefault = function(nextMillesime, actuelMillesime) {
    switch ($("#type_transaction input:checked").val()) {
        case "<?php echo VracClient::TYPE_TRANSACTION_MOUTS ?>":
        case "<?php echo VracClient::TYPE_TRANSACTION_RAISINS ?>":
            $("div#millesime label").text('Récolte');
            $("div#millesime > input").val(nextMillesime);
            $("div#millesime > input").change();
            break;

        case "<?php echo VracClient::TYPE_TRANSACTION_VIN_VRAC ?>":
        case "<?php echo VracClient::TYPE_TRANSACTION_VIN_BOUTEILLE ?>":
            $("div#millesime label").text('Millésime');
            $("div#millesime > input").val(actuelMillesime);
            $("div#millesime > input").change();
            break;
    }
}

var refreshContratsSimilaire = function(integrite, ajaxParams)
{
    if (integrite)
    {
        $.get('getContratsSimilaires', ajaxParams,
                function(data)
                {
                    $('#contrats_similaires').html(data);
                });
    }
}


var getContratSimilaireParams = function(ajaxParams, ui)
{
    var type = $('#type_transaction input:checked').val();
    if (type == '')
        return false;
    ajaxParams['type'] = type;

    if (ui == null) {
        ajaxParams['produit'] = $('#produit option:selected').val();
    }
    else {
        ajaxParams['produit'] = ui.item.option.value;
    }

    var volume = $('#volume_total').val();
    if ((volume != '') && (ajaxParams['produit'] == ''))
        return false;
    ajaxParams['volume'] = volume;

    return true;
}

var init_ajax_nouveau = function()
{
    //$('#vrac_vendeur_famille_viticulteur').attr('checked','checked');
    //$('#vrac_acheteur_famille_negociant').attr('checked','checked');

    ajaxifyAutocompleteGet('getInfos', '#vendeur_choice', '#vendeur_informations');
    ajaxifyAutocompleteGet('getInfos', '#acheteur_choice', '#acheteur_informations');
    ajaxifyAutocompleteGet('getInfos', '#mandataire_choice', '#mandataire_informations');
    $('#has_mandataire input').attr('checked', 'checked');
    $('#vrac_mandatant_acheteur').attr('checked', 'checked');

    majAutocompleteInteractions('vendeur');
    majAutocompleteInteractions('acheteur');
    majAutocompleteInteractions('mandataire');
    majMandatairePanel();
};

var clearVolumesChamps = function()
{
    $('#volume_total').val('');

    //mouts et vracs
    $('#vrac_raisin_quantite').val('');

    //mouts et vracs
    $('#vrac_jus_quantite').val('');


    //conditionné
    $('#vrac_bouteilles_quantite').val('');
    $('#volume_unite_total').html('');
    $('#prix_initial_unitaire_hl').html('');
    $('#prix_unitaire_hl').html('');

    //tout
    $('#vrac_prix_initial_unitaire').val('');
    $('#vrac_prix_initial_total').html('');
    $('#vrac_prix_initial_unite').html('');

    $('#vrac_prix_unitaire').val('');
    $('#vrac_prix_total').html('');
    $('#vrac_prix_unite').html('');

    $('#prixInitialUnitaire span#prix_initial_unitaire_unite').text('');
    $('#prixInitialUnitaire span#prix_unitaire_unite').text('');

};

var majAutocompleteInteractions = function(type)
{
    $('#' + type + '_choice input').live("autocompleteselect", function(event, ui)
    {
        $('#' + type + '_modification_btn').removeAttr('disabled');
        $('#' + type + '_modification_btn').css('cursor', 'pointer');
    });
};

var majModificationsButton = function(type)
{
    if ($('#' + type + '_choice input.ui-autocomplete-input').val() === "")
        $('#' + type + '_modification_btn').attr('disable', 'disable');
    else
        $('#' + type + '_modification_btn').removeAttr('disable');
};


var majMandatairePanel = function()
{
    if ($('#has_mandataire input').attr('checked')) {
        $('#mandataire').show();
    }
    else {
        $('#mandataire').hide();
    }

    $('#has_mandataire input').click(function()
    {
        if ($(this).attr('checked'))
        {
            $('#mandataire').show();
            $('#vrac_mandatant_acheteur').attr('checked', 'checked');
        }
        else
        {
            $('#mandataire').hide();
            $('#mandataire input').each(function()
            {

                if ($(this).attr('type') == 'checkbox')
                    $(this).attr('checked', false);
                else
                {
                    //if($(this).attr('type')!='button') $(this).val('');
                }
            });
        }
    });

    $('#vrac_mandatant_vendeur').click(function()
    {
        if (($('#mandatant input:checked').length === 0) && ($('#vrac_mandatant_vendeur:checked')))
            $('#vrac_mandatant_vendeur').attr('checked', 'checked');
    });
    $('#vrac_mandatant_acheteur').change(function()
    {
        if (($('#mandatant input:checked').length === 0) && ($('#vrac_mandatant_acheteur:checked')))
            $('#vrac_mandatant_acheteur').attr('checked', 'checked');
    });

};

var init_ajax_modification = function(type)
{
    $('a#' + type + '_modification_btn').html('Valider');
    $('a#' + type + '_modification_btn').removeClass('btn_modifier').addClass('btn_valider');

    $('div#' + type + '_annulation_div').show();

    $("#" + type + "_choice input").attr('disabled', 'disabled');
    $("#" + type + "_choice button").attr('disabled', 'disabled');
    $('.btnValidation button').attr('disabled', 'disabled');
    var params = {id: $("#vrac_" + type + "_identifiant").val(), div: '#' + type + '_informations'};
    ajaxifyPost('modification?id=' + $("#vrac_" + type + "_identifiant").val(), params, '#' + type + '_modification_btn', '#' + type + '_informations');

    ajaxifyGet('getInfos', '#vrac_' + type + '_identifiant',
            '#' + type + '_annulation_btn',
            '#' + type + '_informations');

};



var bindEnterModif = function(div, relai)
{
    $(div).keypress(function(e) {
        if (e.keyCode == 13) {
            $(relai).click();
            return false;
        }
    });
}

var bindEnterValid = function()
{
    $(document).keypress(function(e) {
        if (e.keyCode == 13) {
            alert('entrer');
            $('#btn_soussigne_submit').click();
            return false;
        }
    });
}

var init_informations = function(type)
{
    $("#" + type + "_choice input").removeAttr('disabled');
    $("#" + type + "_choice button").removeAttr('disabled');

    $("a#" + type + "_modification_btn").html("Modifier");
    $("a#" + type + "_modification_btn").removeClass('btn_valider').addClass('btn_modifier');

    $('div#' + type + '_annulation_div').hide();

    $("#" + type + "_modification_btn").unbind();
    $("#" + type + "_annulation_btn").unbind();

    $('.btnValidation button').removeAttr('disabled');
    //  bindEnterValid();
};

/*    
 var ajax_send_contrats_similairesSoussigne = function(num_contrat,soussigneType)
 {
 var types = ['vendeur','acheteur','mandataire'];
 $('#'+soussigneType+'_choice input').live( "autocompleteselect", function(event, ui)
 {  
 var ajaxParams = {numero_contrat : num_contrat, 'etape' : 'soussigne'};        
 for (var i in types)
 {        
 var name = types[i];
 if(name!=soussigneType)
 {
 ajaxParams[name] = $('#'+name+'_choice option:selected').val();
 }
 else
 {
 ajaxParams[name] = ui.item.option.value;
 }
 }
 
 $.get('getContratsSimilaires',ajaxParams,
 function(data)
 {
 $('#contrats_similaires').html(data);
 });
 });
 }
 */

var ajax_send_contrats_similairesMarche = function(num_contrat)
{
    var reg0 = (new RegExp("^[0-9]*$", "g"));
    var reg1 = (new RegExp("^[0-9]*[.][0-9]{2}$", "g"));

    $('#vrac_marche #type_transaction input').click(function()
    {
        var type = $(this).filter(':checked').val();
        var prod = $('section#produit option:selected').val();
        var vol = $('#volume div.jus_quantite input').val();

        //    alert('toPOST : ['+type+','+prod+','+vol+']');
    });

    $('section#produit input').live("autocompleteselect", function(event, ui)
    {
        var ajaxParams = {numero_contrat: num_contrat, 'produit': ui.item.option.value, 'etape': 'marche'};
        var vol = $(this).val();
        //if(reg0.test(vol) || reg1.test(vol))

    });
};

var initDatepicker = function() {

    $(".champ_datepicker input").datepicker({
        showOn: "button",
        buttonImage: "/images/pictos/pi_calendrier.png",
        buttonImageOnly: true,
        dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
        monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre"],
        dateFormat: 'dd/mm/yy',
        firstDay: 1
    });
};

var initValidation = function()
{
    $('#btn_validation').click(function()
    {
        $('form#vrac_validation').submit();
    });
};

var initValidationWithPopup = function()
{
    $('#btn_validation').click(function()
    {
        $('.btn_popup').trigger('click');
        $('#popup_validation').click(function()
        {
            $('form#vrac_validation').submit();
        });
    });

};


var setGreyPanel = function(divId)
{
    var content = '<div id="' + divId + '_overlay" class="block" style="position: absolute; top:0; left: 0; right: 0; bottom: 0; background-color: white; opacity: 0.7;"></div>';

    $('#' + divId).append(content);
};

var removeGreyPanel = function(divId) {
    $('#' + divId + '_overlay').remove();
};

$(document).ready(function()
{
    initConditions();
    //$("#vrac_soussigne").bind("submit", function() {return false;});
    $("#btn_soussigne_submit").bind("click", function() {
        $("#vrac_soussigne").unbind("submit");
        $("#vrac_soussigne").submit()
    });
    $('#vendeur_choice input').focus();
    initDatepicker();
});
