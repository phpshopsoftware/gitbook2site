#!/bin/bash

cd "$(dirname "$0")" || exit 1

LOGFILE="./update.log"
exec >> "$LOGFILE" 2>&1

echo "[$(date)] Starting GitHub update..."

wget --timeout=30 --tries=3 --max-redirect=5 \
  --header="Authorization: Bearer ****_MY_TOKEN_****" \
  --header="Accept: application/vnd.github+json" \
  -O main.zip \
  https://api.github.com/repos/phpshopsoftware/help/zipball/main

[ -f main.zip ] || { echo "Download failed"; exit 1; }

rm -rf ./help-main
unzip -o main.zip
mv phpshopsoftware-help-*/ help-main/
rm -f main.zip

echo "[$(date)] Done."
