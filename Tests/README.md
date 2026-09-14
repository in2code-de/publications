# Importer tests

Install development dependencies with `composer install`, then run:

```sh
composer test:unit
```

These PHPUnit tests exercise the public `convert()` methods with real BibTeX
and XML files. They require no TYPO3 bootstrap, database, or running web server.
They cover parsing and conversion, not database persistence or the import UI.

`Unit/Import/Importer/Fixtures/biblatex-examples.bib` is an example file taken from.
https://github.com/plk/biblatex/blob/dev/bibtex/bib/biblatex/biblatex-examples.bib
It contains BibLaTeX example entries, including string macros, cross-references, multiple
authors and LaTeX accents.
The tests check representative fields and all 92 citation identifiers, rather
than snapshotting parser metadata or claiming full BibLaTeX support.
The importer excludes the eight `@string` definitions after parsing. The collection
test verifies that only 92 citation entries are returned and that journal macros
are still resolved.

`publication.bib` and `publications.xml` are small synthetic fixtures for field
mapping, publication types, author conversion, XML entities and CDATA. The XML
fixture includes single-author, multiple-author and authorless publications.

`links.bib` covers issue #35: URL, access-date and file-link mappings, preservation
of native link fields and `pmid`, and URLs containing commas. The abstract importer
test also verifies that self-mappings preserve empty and null values while ordinary
field renaming continues to work.
