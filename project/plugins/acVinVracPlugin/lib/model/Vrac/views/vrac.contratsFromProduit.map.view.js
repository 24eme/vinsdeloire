function(doc) {
  	if ((doc.type != "Vrac") || (doc.valide.statut == null)) {

 		return;
 	}
    	emit([doc.valide.statut, doc.vendeur_identifiant, doc.produit, doc.type_transaction],[doc.numero_archive, doc.acheteur.nom, doc.volume_propose, doc.volume_enleve, doc.vendeur.nom]);
}