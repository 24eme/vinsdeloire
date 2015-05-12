function(doc) {

    if (doc.type != 'DS') {
        return;
    }

    if (doc.statut != "VALIDE") {
        return;
    }

    for (hash_produit in doc.declarations) {
        var produit = doc.declarations[hash_produit];
        var societe = null;
        var stock_elaboration = null;
        if(produit.stock_elaboration) {
            stock_elaboration = produit.stock_elaboration; 
        }
        emit([doc.campagne, societe, doc.identifiant, produit.produit_hash, doc.periode, doc._id], [produit.stock_declare, stock_elaboration, produit.vci, produit.reserve_qualitative, doc.declarant.nom, produit.produit_libelle]);
    }
}
