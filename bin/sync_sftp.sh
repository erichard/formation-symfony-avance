#! /bin/bash

set -euxo pipefail

IMPORT_DIR=${IMPORT_DIR:-"var/imports"}
FILE_AGE=${FILE_AGE:-"24h"}

rclone copy --max-age=$FILE_AGE sftp:workdir/clog_rpt/STORPT/ $IMPORT_DIR/clog/stocks/
rclone copy --max-age=$FILE_AGE sftp:workdir/orli_lh/stocks $IMPORT_DIR/orliweb/stocks/
rclone copy --max-age=$FILE_AGE sftp:workdir/orli/PlateformTampon/DICO $IMPORT_DIR/orliweb/
rclone copy --max-age=$FILE_AGE sftp:workdir/orli/PlateformTampon/VN $IMPORT_DIR/orliweb/

