function(doc) {
  	if (doc.type != "Vrac") {

 		return;
 	}
        if ((doc.valide.statut != "SOLDE") && (doc.valide.statut != "NONSOLDE")) {
 		return;
 	}
    mandataire = '';
    if (doc.mandataire_identifiant) {
	mandataire = doc.mandataire_identifiant;
    }
    emit([doc.vendeur_identifiant, doc.acheteur_identifiant, mandataire, doc.type_transaction, doc.produit, doc.volume_propose],[doc.numero_contrat, doc.valide.statut, doc.millesime , doc.volume_propose, doc.numero_archive]);
}