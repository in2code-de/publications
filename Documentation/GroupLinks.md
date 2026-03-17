# Group Links

When **Show group links** is enabled in the plugin FlexForm, anchor links to each group section are rendered
above the publication list. Group links are only active when a **Group by** option other than *none* is
selected.

## Supported groupings

Group links are generated for all built-in groupby options as well as for custom scalar fields registered
via TSConfig. See [Grouping.md](Grouping.md) for details on adding custom groupby fields.

Relation-based field paths (e.g. `authors.lastName`) do not produce group links.

## Override link prefix and suffix

The default pre- and suffix (`[`, `]`) around each link tag can be overridden via TypoScript:

```typo3_typoscript
plugin.tx_publications {
    settings {
        groupLinks {
            linkTag {
                prefix = [
                suffix = ]
            }
        }
    }
}
```