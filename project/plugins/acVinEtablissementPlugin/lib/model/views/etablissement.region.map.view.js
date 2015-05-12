function(doc) {

    if (doc.type != "Etablissement") {
        
        return ;     
    }     
    
    if (doc.cooperative && doc.cooperative != "0") {
        emit(["COOPERATIVE", doc.statut, doc.region, doc.siege.commune, doc.siege.code_postal, doc.cvi, doc.nom, doc.identifiant], 1);
    } else {
        emit([doc.famille, doc.statut, doc.region, doc.siege.commune, doc.siege.code_postal, doc.cvi, doc.nom, doc.identifiant], 1);
    }
}