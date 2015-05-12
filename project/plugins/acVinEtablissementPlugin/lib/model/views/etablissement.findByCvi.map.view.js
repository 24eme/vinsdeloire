function(doc) {
  	if (doc.type == "Etablissement") {
  		emit([doc.cvi],[doc.identifiant, doc.nom, doc.siege.commune]);
 	}
}