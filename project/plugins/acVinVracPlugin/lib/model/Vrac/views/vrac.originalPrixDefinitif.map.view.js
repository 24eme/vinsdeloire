	function(doc) {
  	if ((doc.type != "Vrac") || (doc.valide.statut == null))
    	return;  
  	emit([doc.attente_original, doc.prix_variable, doc.valide.date_saisie, doc.part_variable, doc.vendeur_identifiant, doc.vendeur.nom],1);
}
