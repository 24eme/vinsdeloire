function(doc) {
  	if ((doc.type != "Vrac") || (doc.valide.statut == null))
    	return;  
  	emit([doc.valide.statut, doc.type_transaction,	doc.valide.date_saisie, doc.vendeur_identifiant, doc.vendeur.nom],1);
}