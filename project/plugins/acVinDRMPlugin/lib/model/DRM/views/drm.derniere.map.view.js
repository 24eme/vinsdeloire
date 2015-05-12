function(doc) { 
    if (!(doc.type == "DRM" && doc.valide.date_saisie)) { 

        return;
    }

    emit([doc.campagne, doc.periode], 1);  
}