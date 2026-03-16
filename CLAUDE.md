# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Extension Overview

`in2code/publications` is a TYPO3 14 extension for listing academic publications with BibTeX/XML import/export. It targets universities and colleges, offering frontend list views with filters, pagination, cite styles, and export functionality.

- **Namespace:** `In2code\Publications\`
- **Extension key:** `publications`
- **TYPO3:** ^14.1 | **PHP:** ^8.2
- **Vendor dir:** `.build/vendor/` | **Bin dir:** `.build/bin/`

## Development Environment

Requires **DDEV** and **git-lfs** (database fixture and fileadmin are stored in LFS):

```bash
ddev start
ddev initialize   # pulls LFS files, imports DB, symlinks config, runs composer install
ddev ssh          # enter container for composer/CLI commands
```

`ddev initialize` creates symlinks from `.project/config/typo3/` into `config/` and imports the test database from `.project/data/db.sql.gz`.

## Commands

```bash
# Code style check (dry-run)
composer test:php:cs

# Auto-fix code style
composer fix:php

# PHP syntax lint (parallel, 4 processes)
composer test:php:lint

# TypoScript lint
composer test:typoscript:lint

# CLI commands (inside DDEV container)
./vendor/bin/typo3cms publications:migrate   # migrate from bib extension
./vendor/bin/typo3cms publications:clean 0   # delete all publication records
./vendor/bin/typo3cms publications:clean 123 # delete records on page uid 123
```

There are **no PHPUnit tests** — quality assurance is linting-only. PHP-CS-Fixer rules are in `.project/tests/.php-cs-fixer.php` and scan `Classes/` and `Configuration/`.

## Architecture

### Domain Flow

```
Frontend Request
  → PublicationController::initializeListAction() / listAction(Filter)
  → setFilterArguments(): loads session state, builds Filter DTO, saves back to session
  → PublicationRepository::findByFilter(Filter)
  → ArrayPaginator + NumberedPagination
  → Fluid template with cite style partial
```

All four plugin actions (`list`, `resetList`, `downloadBibtex`, `downloadXml`) are registered as **uncached** in `ext_localconf.php`.

### Key Layers

**Controllers** (`Classes/Controller/`)
- `PublicationController` — frontend plugin; `setFilterArguments()` is the entry point for filter state
- `ImportController` — backend module; delegates to `ImportService` for file handling

**Domain** (`Classes/Domain/`)
- `Model/Publication` — 56+ fields covering all academic publication metadata
- `Model/Author` — firstName, lastName, url, orcid
- `Model/Dto/Filter` — stateful DTO; constants `GROUP_BY_NONE(-1)`, `GROUP_BY_YEAR(0)`, `GROUP_BY_TYPE(1)`, `GROUP_BY_YEAR_AND_TYPE(2)`; `isGroupbySet()` compares against `GROUP_BY_NONE`
- `Repository/PublicationRepository` — `findByFilter()` applies all active constraints; full-text search spans 14 fields including author relations
- `Service/PublicationService` — `getGroupedPublicationLinks()` builds anchor-link navigation for grouped views

**Filter Persistence**
Session key pattern: `filter_{contentElementUid}` (managed by `SessionUtility`). `setFilterArguments()` in `PublicationController` reads from session, merges request arguments, and writes back.

**Import Pipeline** (`Classes/Import/`, `Classes/Service/`)
- `ImportController` → `ImportService` (file upload/validation) → `Importer` (parse + persist)
- `Importer/ImporterInterface` → `AbstractImporter` → `BibImporter` / `XmlImporter`
- `BibImporter` uses `renanbr/bibtex-parser` with `AuthorProcessor` and `SpecialCharProcessor`
- Custom importers are registered in `ext_tables.php`:
  ```php
  $GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer']['KEY'] = MyImporter::class;
  ```

**Other Classes**
- `Classes/Command/` — `ClearCommand`, `MigrationCommand` (registered via `Services.yaml` tags)
- `Classes/Pagination/NumberedPagination.php` — custom paginator wrapper
- `Classes/Tca/CitestyleSelection.php` — dynamic TCA items callback for cite style dropdown
- `Classes/Upgrades/PublicationCTypeUpdate.php` — upgrade wizard for CType migration
- `Classes/Validation/Validator/` — `ClassValidator`, `UploadValidator` for import form

**Utilities** (`Classes/Utility/`)
- `SessionUtility` — persists filter state per content element UID
- `BibTexUtility`, `DatabaseUtility`, `FrontendUtility`, `ConfigurationUtility`, `PageUtility`, `ObjectUtility`

**ViewHelpers** (`Classes/ViewHelpers/`)
- Format: `HighlightAuthor`, `Initials`, `EncodeBibTex`, `RemoveEmptyLines`, `Ucfirst`, `MonthNameFromNumber`
- Render: `Icon`, `Eol`, `Ascii`
- Registered as Fluid namespace `publications:` in `ext_localconf.php`

### Cite Styles

9 Fluid partials in `Resources/Private/Partials/Publication/Citestyle/Citestyle{N}.html`. New cite styles can be added via Page TSConfig — see `Documentation/Citestyles.md`. The TCA dropdown is populated dynamically by `CitestyleSelection`.

### Configuration Structure

| Path | Purpose |
|------|---------|
| `Configuration/TCA/` | Publication and Author table definitions |
| `Configuration/TCA/Overrides/` | tt_content, pages, sys_template overrides |
| `Configuration/FlexForms/FlexFormPi1.xml` | Plugin configuration (cite style, filters, grouping) |
| `Configuration/TypoScript/` | setup.typoscript + constants.typoscript |
| `Configuration/TsConfig/Page/` | TSConfig for cite styles and tceform |
| `Configuration/Backend/Modules.php` | Import backend module registration |
| `Configuration/Services.yaml` | DI autowiring; excludes `Domain/Model/*` |

### Dependency Injection

All services use constructor injection with `private readonly`. `Services.yaml` autowires everything under `In2code\Publications\` except domain models. The `Filter` DTO is instantiated via `GeneralUtility::makeInstance(Filter::class, $this->settings)` because it requires settings passed at construction time.

## Commit Message Style

Follow the project's bracket prefix pattern:

```
[TASK] description of task
[FEATURE] description of feature
[BUGFIX] description of fix
[CLEANUP] description of cleanup
```
