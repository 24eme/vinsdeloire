function(doc) {
  	if (doc.type == "Etablissement") {
  		emit(doc.service_douane, [
			  doc.interpro,
  			  doc._id, 
  			  doc.nom, 
		  	  doc.identifiant, 
		  	  doc.raison_sociale, 
		  	  doc.siret, 
		  	  doc.cvi, 
  			  doc.famille, 
		  	  doc.siege.commune, 
		      doc.siege.code_postal]);
 	}
}