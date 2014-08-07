#!/bin/bash

. bin/config.inc

FORCE_PUT_DOC_FILTER=$1

DOC_FILTER_ID=replication_teledeclaration
DOC_FILTER_REV=$(curl -s "http://$COUCHHOST:$COUCHPORT/$COUCHBASE/_design/replication_teledeclaration" | grep -Eo "_rev\":\"[0-9a-Z-]+\"" | sed 's/_rev":"//' | sed 's/"//')

if ! test "$DOC_FILTER_REV" || test $FORCE_PUT_DOC_FILTER ; then

    if test "$DOC_FILTER_REV" ; then
        DOC_FILTER_JSON_REV="\"_rev\": \"$DOC_FILTER_REV\"",
    else
        DOC_FILTER_JSON_REV=""
    fi

    echo "{
       \"_id\": \"_design/$DOC_FILTER_ID\", $DOC_FILTER_JSON_REV
       \"filters\": {
           \"$DOC_FILTER_ID\": \"function(doc, req) { if (!doc.type) { return false; } if(doc.type == \\\"Vrac\\\" || doc.type == \\\"Etablissement\\\" || doc.type == \\\"Compte\\\" || doc.type == \\\"Societe\\\" || doc.type == \\\"Configuration\\\") { return true; } return false; }\"
       }
    }" > /tmp/doc_replication_teledeclaration.json

    curl -H "Content-Type: application/json" -X PUT -d '@/tmp/doc_replication_teledeclaration.json' "http://$COUCHHOST:$COUCHPORT/$COUCHBASE/_design/$DOC_FILTER_ID"
fi

echo "{\"source\":\"$COUCHBASE\",\"target\":\"$REPLICATE_TELEDECLARATION_URL\",\"filter\":\"$DOC_FILTER_ID/$DOC_FILTER_ID\",\"continuous\": true}" > /tmp/params_replication_teleclaration.json

curl -H "Content-Type: application/json" -X POST -d '@/tmp/params_replication_teleclaration.json' "http://$COUCHHOST:$COUCHPORT/_replicate"