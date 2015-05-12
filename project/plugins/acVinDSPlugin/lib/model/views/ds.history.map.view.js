function(doc) {
    if (doc.type != "DS") {
        return;
    }

    emit([doc.identifiant, doc.campagne, doc.periode, doc.statut], [doc._id, doc.declarant.cvi, doc.numero_archive]);

}