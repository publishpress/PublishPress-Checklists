#!/bin/sh

# Sync the src folder into wordpress, to auto-update the code while
# developing. Used with fswatch in Mac OS to detect file changes.

STAGING_PATH=~/Projects/OSTraining/Git/dev-env-wordpress/www/wp-content/plugins/publishpress-content-checklist
SRC_PATH=../plugin


if [ -d $STAGING_PATH ]; then
	rm -rf $SRC_PATH/*
	cp -R $STAGING_PATH/* $SRC_PATH/
fi
