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

`unpublished.bib` covers issue #90: an unpublished entry with year, note, language and
institution, a capitalized `@Unpublished` entry without optional fields, and a note
ending in a period combined with a month. `publications.xml` contains the same
unpublished example as its fourth reference.

`links.bib` covers issue #35: URL, access-date and file-link mappings, preservation
of native link fields and `pmid`, and URLs containing commas. The abstract importer
test also verifies that self-mappings preserve empty and null values while ordinary
field renaming continues to work.

# Citation style tests

The functional tests render the list plugin as a frontend request in a TYPO3 test
instance with an SQLite database. They require no running web server or database
server. Run them inside ddev, so they use the PHP version and extensions of the
project (including `pdo_sqlite`):

```sh
ddev composer test:functional
```

`Functional/Frontend/CitestyleTest.php` covers issue #90. It renders three
unpublished publications from `Functional/Frontend/Fixtures/UnpublishedPublications.csv`
in the default, IEEE, APA, IEEE with abstract and Vancouver-based citation styles,
each on a German and an English site language. The records cover a note used as
status, a note ending in a period, the localized fallback without a note, a missing
year, a month abbreviation (German "Mrz", English "Mar"), the institution, the
publication language and the abstract.

The test compares each citation as the browser displays it: tags are removed, line
breaks become ` | ` and whitespace is collapsed. Spaces before or after punctuation
are kept, so spacing errors such as "Example,Uma" make the test fail.
