function(doc) {
    if (doc.type != "Configuration")
    return ;

    var inter = new Array();
    var code_douane = null;

    for (c in doc.declaration.certifications) {
        var interpros = new Array();
        for(interpro_key in doc.declaration.certifications[c].interpro) {
            interpros.push(interpro_key);
        }
        inter.unshift(interpros);
        if (!code_douane) { code_douane = doc.declaration.certifications[c].code_douane; }
        for (g in doc.declaration.certifications[c].genres) {
            var interpros = new Array();
            for(interpro_key in doc.declaration.certifications[c].genres[g].interpro) {
                interpros.push(interpro_key);
            }
            inter.unshift(interpros);
            if (!code_douane) { code_douane = doc.declaration.certifications[c].genres[g].code_douane; }
            for (a in doc.declaration.certifications[c].genres[g].appellations) {
                var interpros = new Array();
                for(interpro_key in doc.declaration.certifications[c].genres[g].appellations[a].interpro) {
                    interpros.push(interpro_key);
                }
                inter.unshift(interpros);
                if (!code_douane) { code_douane = doc.declaration.certifications[c].genres[g].appellations[a].code_douane; }
                for (m in doc.declaration.certifications[c].genres[g].appellations[a].mentions) {
                    if (!code_douane) { code_douane = doc.declaration.certifications[c].genres[g].appellations[a].mentions[m].code_douane; }
                    for(l in doc.declaration.certifications[c].genres[g].appellations[a].mentions[m].lieux) {
                        if (!code_douane) { code_douane = doc.declaration.certifications[c].genres[g].appellations[a].mentions[m].lieux[l].code_douane; }
                        for(co in doc.declaration.certifications[c].genres[g].appellations[a].mentions[m].lieux[l].couleurs) {
                            if (!code_douane) { code_douane = doc.declaration.certifications[c].genres[g].appellations[a].mentions[m].lieux[l].couleurs[co].code_douane; }
                            for(ce in doc.declaration.certifications[c].genres[g].appellations[a].mentions[m].lieux[l].couleurs[co].cepages) {
                                if (!code_douane) { code_douane = doc.declaration.certifications[c].genres[g].appellations[a].mentions[m].lieux[l].couleurs[co].cepages[ce].code_douane; }
                                var hash = "/declaration/certifications/"+c+"/genres/"+g+"/appellations/"+a+"/mentions/"+m+"/lieux/"+l+"/couleurs/"+co+"/cepages/"+ce;
                                for(i in inter) {
                                    if (inter[i].length > 0) {
                                        for(array_intepro_key in inter) {
                                            emit([inter[i][array_intepro_key], code_douane, hash], null);         
                                            break;
                                        }
                                        break;
                                    }
                                }
                                code_douane = null;
                            }
                        }
                    }
                }
                inter.splice(0,1);
            }
            inter.splice(0,1);
        }
        inter.splice(0,1);
    }
}