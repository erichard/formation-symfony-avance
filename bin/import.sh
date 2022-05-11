#! /bin/bash

set -euxo pipefail

IMPORT_DIR=${IMPORT_DIR:-"var/imports"}
CONSOLE=${CONSOLE:-"php bin/console"}

DATE=$(date '+%Y%m%d%H%M')
ARCHIVE_DIR=${ARCHIVE_DIR:-"$IMPORT_DIR/archives/$DATE"}
mkdir -p "$ARCHIVE_DIR/orliweb/stocks"
mkdir -p "$ARCHIVE_DIR/clog/stocks"

if [ -f "$IMPORT_DIR/orliweb/marque.csv" ]; then
$CONSOLE app:orliweb:import-marques $IMPORT_DIR/orliweb/marque.csv \
    && mv $IMPORT_DIR/orliweb/marque.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/forme.csv" ]; then
$CONSOLE app:orliweb:import-formes $IMPORT_DIR/orliweb/forme.csv \
    && mv $IMPORT_DIR/orliweb/forme.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/type_fermeture.csv" ]; then
$CONSOLE app:orliweb:import-fermetures $IMPORT_DIR/orliweb/type_fermeture.csv \
    && mv $IMPORT_DIR/orliweb/type_fermeture.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/typ_fab.csv" ]; then
$CONSOLE app:orliweb:import-fabrications $IMPORT_DIR/orliweb/typ_fab.csv \
    && mv $IMPORT_DIR/orliweb/typ_fab.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/semelle.csv" ]; then
$CONSOLE app:orliweb:import-semelles $IMPORT_DIR/orliweb/semelle.csv \
    && mv $IMPORT_DIR/orliweb/semelle.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/ligne_produit.csv" ]; then
$CONSOLE app:orliweb:import-lignes $IMPORT_DIR/orliweb/ligne_produit.csv \
    && mv $IMPORT_DIR/orliweb/ligne_produit.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/genre.csv" ]; then
$CONSOLE app:orliweb:import-genres $IMPORT_DIR/orliweb/genre.csv \
    && mv $IMPORT_DIR/orliweb/genre.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/composition.csv" ]; then
$CONSOLE app:orliweb:import-compositions $IMPORT_DIR/orliweb/composition.csv \
    && mv $IMPORT_DIR/orliweb/composition.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/article.csv" ]; then
$CONSOLE app:orliweb:import-articles $IMPORT_DIR/orliweb/article.csv \
    && mv $IMPORT_DIR/orliweb/article.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/produit.csv" ]; then
$CONSOLE app:orliweb:import-produits $IMPORT_DIR/orliweb/produit.csv \
    && mv $IMPORT_DIR/orliweb/produit.csv "$ARCHIVE_DIR/orliweb/"
fi

if [ -f "$IMPORT_DIR/orliweb/tarif.csv" ]; then
$CONSOLE app:orliweb:import-tarifs $IMPORT_DIR/orliweb/tarif.csv \
    && mv $IMPORT_DIR/orliweb/tarif.csv "$ARCHIVE_DIR/orliweb/"
fi

$CONSOLE app:orliweb:import-stock   --dir $IMPORT_DIR/orliweb/stocks/ARCHIVES \
    && mv $IMPORT_DIR/orliweb/stocks/ARCHIVES/* "$ARCHIVE_DIR/orliweb/stocks/"

$CONSOLE app:clog:import-stock      --dir $IMPORT_DIR/clog/stocks \
    && mv $IMPORT_DIR/clog/stocks/* "$ARCHIVE_DIR/clog/stocks/"

$CONSOLE app:reset-stock-scheduled
