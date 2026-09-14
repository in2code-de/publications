# Upgrade notes

## BibTeX import field mappings ([Github Issue #35](https://github.com/in2code-de/publications/issues/35))

The BibTeX importer now uses the following mappings:

| Input field | Publication field |
| --- | --- |
| `url` | `web_url` |
| `urldate` | `web_url_date` |
| `file` | `file_url` |

Previously, `url` was imported into `file_url`. If your import files used `url`
to populate the publication's file link, use `file` or `file_url` instead.
Keep `url` for the publication's web address.

This change affects future imports. Existing publication records are not
automatically migrated.

If both a source field and its mapped destination are present, the source field
overwrites the destination, as with other mappings. For example, if an entry
contains both `url` and `web_url`, the value of `url` is imported into `web_url`.

The importer also preserves `web_url` and `pmid`, which were previously deleted
by redundant self-mappings.

## Longer publication annotations

The `annotation` column now uses `TEXT` instead of `VARCHAR(255)` to support
longer annotations, such as those in
https://github.com/plk/biblatex/blob/dev/bibtex/bib/biblatex/biblatex-examples.bib
without truncation.

After updating the extension, run TYPO3's database schema analysis in the
Maintenance module and apply the change to
`tx_publications_domain_model_publication.annotation` before importing publications.
Existing annotation values are preserved.
