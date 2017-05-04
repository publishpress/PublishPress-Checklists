#!/bin/sh

#==========================================
#            Check the env vars           =
#==========================================

[ -z "$WP_PATH" ] && echo "Need to set WP_PATH" && exit 1;
[ ! -f "$WP_PATH/wp-config.php" ] && echo "WordPress not found on: $WP_PATH." && echo "Check the WP_PATH env var." && exit 1;

#=====  End of Check the env vars  ======


#==========================================
#                The script               =
#==========================================
# Sync the src folder into wordpress, to auto-update the code while
# developing. Used with fswatch in Mac OS to detect file changes.

STAGING_PATH=$WP_PATH/wp-content/plugins/publishpress-content-checklist
SRC_PATH=../plugin


if [ ! -d $STAGING_PATH ]; then
	mkdir $STAGING_PATH
fi

rm -rf $STAGING_PATH/*
cp -R $SRC_PATH/* $STAGING_PATH