function(doc) {
  	if ((doc.type != "Vrac") || (doc.valide.statut == null)) {
    	
    	return;
    }
  	
  	if (doc.domaine) {
  		
  		emit([doc.vendeur_identifiant, doc.domaine],  1);
  	}
}