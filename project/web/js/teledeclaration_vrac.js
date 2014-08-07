

var initSignaturePopup = function() {

    $('a.signature_popup').fancybox({
        autoSize: true,
        autoCenter: true,
        height: 'auto',
        width: 'auto',
    });
};

var triggerSignaturePopup = function() {
    $('a.signature_popup').click();
}

var initRechercheFiltre = function() {

    $('form#filtres_historique select#campagne').change(function() {
        $('form#filtres_historique').submit();
    });

    $('form#filtres_historique select#etablissement').change(function() {
        $('form#filtres_historique').submit();
    });


}

var initTeledeclarationCourtierSoussigne = function() {

    $("#teledeclaration_courtier_interlocuteur_commercial_show").change(function() {
        if($(this).is(":checked")){
            $("#teledeclaration_courtier_interlocuteur_commercial").show();
        }else{
            $("#teledeclaration_courtier_interlocuteur_commercial").hide();
        }
    })

}

$(document).ready(function()
{
    initSignaturePopup();
    initRechercheFiltre();
});

