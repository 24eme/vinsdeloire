function(doc) {
    if ((doc.type != "Vrac") || (doc.valide.statut != 'NONSOLDE')) {
        
        return;
    }

    emit([doc.campagne, null, doc.vendeur_identifiant, doc.type_transaction, doc.produit, doc.numero_contrat], doc.volume_propose - doc.volume_enleve);
}