---
name: Release the Free version (team only)
about: Describes default checklist for the plugin's release process.
title: Release PublishPress Checklists v[VERSION]
labels: release
assignees: ''
---

To release the Free plugin please make sure to check all the checkboxes below.

### Pre-release Checklist
- [ ] Create the release branch as `release-<version>` based on the development branch.
- [ ] Review and merge all the relevant Pull Requests into the release branch.
- [ ] Start a dev-workspace session.
- [ ] Run `composer update` (updating the root and lib vendors).
- [ ] Double-check the updated packages, and if any library used on production was updated mention that in the changelog.
- [ ] Check Github's Dependabot warnings or pull requests, looking for any relevant report. Remove any false-positive first. Fix and commit the fix for the issues you find.
- [ ] If applied, build JS files to production running `composer build:js` and commit the changes.
- [ ] Run WP VIP scan to make sure no warnings or errors > 5 exists: `composer check:phpcs`.
- [ ] Update the `.pot` file, and mention this on the changelog.
- [ ] For minor and patch releases, implement backward compatibility for changes like: renamed or moved classes, namespaces, functions, constants, hooks or global variables. Add the proper deprecated comments and mention that in the changelog. Major releases can remove old deprecated API or code, making sure to always mention that in the changelog.
- [ ] Update the changelog - make sure all the changes are there with a user-friendly description and that the release date is correct.
- [ ] Update the version number to the next stable version in the main plugin file and `readme.txt` file, following [Semantic Versioning](https://semver.org/). Commit the changes to the release branch.
- [ ] Make sure there is no uncommitted changes.
- [ ] Build the zip package, running `composer build`. It should create a new package in the `./dist` dir.
- [ ] Send the new package to the team for testing.

### Release Checklist
- [ ] Create a Pull Request and merge the release branch it into the `master` branch.
- [ ] Merge the `main` branch into the `development` branch.
- [ ] Create the GitHub release (make sure it is based on the `main` branch and correct tag).

#### WP SVN Deployment
- [ ] Enter the local copy of SVN repo for the plugin.
- [ ] Update your working copy using `svn update`.
- [ ] Cleanup the `trunk` directory running `rm -rf trunk/*`.
- [ ] Unzip the built package and move files to the `trunk` folder.
- [ ] Remove any eventual file that shouldn't be released in the package (if you find anything, make sure to create an issue to fix the `.rsync-filter-post-build` file).
- [ ] Look for new files `svn status | grep \?` and add them using `svn add <each_file_path>`.
- [ ] Look for removed files `svn status | grep !` and remove them `svn rm <each_file_path>`.
- [ ] Create the new tag `svn cp trunk tags/<version>`.
- [ ] Commit the changes `svn ci -m 'Releasing <version>'`.
- [ ] Wait until WordPress updates the version number and make the final test updating the plugin in a staging site.
