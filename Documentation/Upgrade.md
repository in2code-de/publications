# Upgrade notes

## BibTeX import field mappings (issue #35)

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
