function(doc) {
  	if (doc.type == "Compte") {
  		emit([doc.interpro, 
                          doc.statut,
  			  doc._id, 
  			  doc.nom_a_afficher, 
		  	  doc.identifiant, 
		  	  doc.adresse, 
		  	  doc.commune, 
		      doc.code_postal, doc.compte_type], null);
 	}
}
