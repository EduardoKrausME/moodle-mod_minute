# Changelog

This changelog documents only the two most recent published GitHub releases. Each entry records changes verified by comparing its release tag with the immediately preceding published release. Changes made later on `master` are excluded.

## 1.1.9 - 2026-10-15

### Changed

- Updated the GitHub Actions CI PostgreSQL service from version 16 to 17.
- Updated the MariaDB CI service from version 10 to 11.4, replacing the prior startup environment variables and using the `healthcheck.sh --connect --innodb_initialized` health-check command.
- Added a plugin icon at `pix/icon.png` and reorganized the SVG logo asset as `pix/monologo.svg`.
- Updated the plugin release identifier from `1.1.8` to `1.1.9` and its build number from `2026101200` to `2026101500`.

No functional changes to the Minute activity were identified in this tag comparison.

[Compare 1.1.8...1.1.9](https://github.com/EduardoKrausME/moodle-mod_minute/compare/1.1.8...1.1.9)

## 1.1.8 - 2026-10-12

### Fixed

- Hardened the GitHub Actions release workflow against shell injection by reading the head commit message from the `RELEASE_MESSAGE` environment variable instead of interpolating it directly into shell commands.

### Changed

- Updated the PHP 8.3 CI test entry from Moodle 5.2 (`MOODLE_502_STABLE`) to Moodle 5.3 (`MOODLE_503_STABLE`), retaining the Moodle 4.5 test entry.
- Removed the release workflow's automatic `minute.zip` packaging step and its ZIP attachment to the GitHub Release. The workflow still creates a release for a valid version commit.
- Updated the plugin release identifier from `1.1.7` to `1.1.8` and its build number from `2026100600` to `2026101200`.

No functional changes to the Minute activity were identified in this tag comparison.

[Compare 1.1.7...1.1.8](https://github.com/EduardoKrausME/moodle-mod_minute/compare/1.1.7...1.1.8)
