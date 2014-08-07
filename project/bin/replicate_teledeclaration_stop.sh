#!/bin/bash

. bin/config.inc

DOC_FILTER_ID=replication_teledeclaration

echo "{\"source\":\"$COUCHBASE\",\"target\":\"$REPLICATE_TELEDECLARATION_URL\",\"filter\":\"$DOC_FILTER_ID/$DOC_FILTER_ID\",\"continuous\": true, \"cancel\": true}" > /tmp/params_replication_teleclaration.json

curl -H "Content-Type: application/json" -X POST -d '@/tmp/params_replication_teleclaration.json' "http://$COUCHHOST:$COUCHPORT/_replicate"