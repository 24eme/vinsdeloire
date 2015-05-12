function(doc) {
    if (doc.type != "DRM") {
        
        return;     
    }
    
    emit([doc.identifiant, doc.campagne, doc.periode, doc.version, doc.mode_de_saisie, doc.valide.date_saisie, doc.douane.envoi, doc.douane.accuse, doc.numero_archive], 1);
}