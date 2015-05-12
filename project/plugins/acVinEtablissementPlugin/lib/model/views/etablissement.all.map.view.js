function(doc) {
  	if (doc.type != "Etablissement") {

        return;
    }
    
    emit([doc.interpro, doc.statut, doc.famille, doc.id_societe, doc._id, doc.nom, doc.identifiant, doc.cvi, doc.region], [doc.raison_sociale, doc.siege.adresse, doc.siege.commune, doc.siege.code_postal, doc.no_accises, doc.carte_pro, doc.email, doc.telephone, doc.fax, doc.recette_locale.id_douane, doc.recette_locale.nom]);
    if (doc.cooperative && doc.cooperative != "0") {
	emit([doc.interpro, doc.statut, "COOPERATIVE", doc.id_societe, doc._id, doc.nom, doc.identifiant, doc.cvi, doc.region], [doc.raison_sociale, doc.siege.adresse, doc.siege.commune, doc.siege.code_postal, doc.no_accises, doc.carte_pro, doc.email, doc.telephone, doc.fax, doc.recette_locale.id_douane, doc.recette_locale.nom]);
    }
}