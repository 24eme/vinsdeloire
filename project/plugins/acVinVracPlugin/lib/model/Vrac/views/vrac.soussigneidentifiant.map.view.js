function(doc) {
  	if (doc.type != "Vrac")
    	return;

	 campagne = doc.campagne;

    archive = doc.numero_contrat;
    if (doc.numero_archive) {
	archive = doc.numero_archive;
    }

    if (doc.mandataire_identifiant)
	emit(["STATUT", doc.mandataire_identifiant, campagne, doc.valide.statut, doc.type_transaction],  [campagne, doc.valide.statut, doc._id, doc.numero_contrat, archive, doc.acheteur_identifiant, doc.acheteur.nom, doc.vendeur_identifiant, doc.vendeur.nom, doc.mandataire_identifiant,doc.mandataire.nom, doc.type_transaction, doc.produit, doc.produit_libelle, doc.volume_propose, doc.volume_enleve]);
  	
    emit(["STATUT", doc.acheteur_identifiant, campagne, doc.valide.statut, doc.type_transaction], [campagne, doc.valide.statut, doc._id, doc.numero_contrat, archive, doc.acheteur_identifiant, doc.acheteur.nom, doc.vendeur_identifiant, doc.vendeur.nom, doc.mandataire_identifiant,doc.mandataire.nom, doc.type_transaction, doc.produit, doc.produit_libelle, doc.volume_propose, doc.volume_enleve]);
  	
    emit(["STATUT", doc.vendeur_identifiant, campagne, doc.valide.statut, doc.type_transaction], [campagne, doc.valide.statut, doc._id, doc.numero_contrat, archive, doc.acheteur_identifiant, doc.acheteur.nom, doc.vendeur_identifiant, doc.vendeur.nom, doc.mandataire_identifiant,doc.mandataire.nom, doc.type_transaction, doc.produit, doc.produit_libelle, doc.volume_propose, doc.volume_enleve]);
  	
    if (doc.mandataire_identifiant)
	emit(["TYPE", doc.mandataire_identifiant, campagne, doc.type_transaction],  [campagne, doc.valide.statut, doc._id, doc.numero_contrat, archive, doc.acheteur_identifiant, doc.acheteur.nom, doc.vendeur_identifiant, doc.vendeur.nom, doc.mandataire_identifiant,doc.mandataire.nom, doc.type_transaction, doc.produit, doc.produit_libelle, doc.volume_propose, doc.volume_enleve]);
  	
    emit(["TYPE", doc.acheteur_identifiant, campagne, doc.type_transaction], [campagne, doc.valide.statut, doc._id, doc.numero_contrat, archive, doc.acheteur_identifiant, doc.acheteur.nom, doc.vendeur_identifiant, doc.vendeur.nom, doc.mandataire_identifiant,doc.mandataire.nom, doc.type_transaction, doc.produit, doc.produit_libelle, doc.volume_propose, doc.volume_enleve]);
  	
    emit(["TYPE", doc.vendeur_identifiant, campagne, doc.type_transaction], [campagne, doc.valide.statut, doc._id, doc.numero_contrat, archive, doc.acheteur_identifiant, doc.acheteur.nom, doc.vendeur_identifiant, doc.vendeur.nom, doc.mandataire_identifiant,doc.mandataire.nom, doc.type_transaction, doc.produit, doc.produit_libelle, doc.volume_propose, doc.volume_enleve]);
}