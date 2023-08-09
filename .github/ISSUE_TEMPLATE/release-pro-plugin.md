---
name: Release the Pro version (team only)
about: Describes default checklist for the plugin's release process.
title: Release v[VERSION]
labels: release
assignees: ''
---

To release the Pro plugin please make sure to check all the checkboxes below.

### Pre-release Checklist
- [ ] Create the release branch as `release-<version>` based on the development branch.
- [ ] Review and merge all the relevant Pull Requests into the release branch.
- [ ] Start a dev-workspace session.
- [ ] Make sure the correct version of the free plugin is referenced in the `lib/composer.json` file.
- [ ] Run `composer update` (updating the root and lib vendors).
- [ ] Double-check the updated packages, and if any library used on production was updated mention that in the changelog.
- [ ] Check if all dependencies are synced from Free into the Pro plugin, running `composer check:deps`.
- [ ] If required, merge dependencies running `composer fix:deps`. Then run `composer update` again.
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

#### PublishPress.com Deployment
- [ ] Update EDD registry and upload the new package
- [ ] Make the final test updating the plugin in a staging site
