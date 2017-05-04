#!/bin/sh

# Sync the src folder into wordpress, to auto-update the code while
# developing. Used with fswatch in Mac OS to detect file changes.

rm -rf ~/Projects/OSTraining/Git/dev-env-wordpress/www/wp-content/plugins/publishpress-content-checklist/*
cp -R ../plugin/* ~/Projects/OSTraining/Git/dev-env-wordpress/www/wp-content/plugins/publishpress-content-checklist/