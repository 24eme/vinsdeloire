function(doc) {
  	if (doc.type != "SV12") {    	
    	return;
  	}

  	emit([doc.valide.statut], [doc._id, doc.valide.date_saisie, doc.periode, doc.version, doc.identifiant, doc.declarant.nom, doc.declarant.cvi, doc.declarant.commune, doc.valide.statut] );
}