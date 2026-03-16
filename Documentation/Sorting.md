# Sorting

Publications can be sorted via the plugin FlexForm. Three settings control the sort behaviour:

| FlexForm field     | Default | Description                                   |
|--------------------|---------|-----------------------------------------------|
| Group by direction | DESC    | Primary sort direction for the grouping field |
| Sort by            | year    | Secondary sort field within each group        |
| Sort direction     | DESC    | Sort direction for the secondary sort field   |

## Group by direction

Controls the sort direction of the primary grouping field selected in **Group by**.

When **Group by** is set to *none*, the group by direction has no effect.

## Sort by

Determines the secondary sort field within each group. Available fields by default:

| Label              | Field              |
|--------------------|--------------------|
| Year               | `year`             |
| Title              | `title`            |
| Publication type   | `bibtype`          |
| Author (last name) | `authors.lastName` |

### Add custom sort fields via TSConfig

Additional sort fields can be registered per page via TSConfig:

```tsconfig
tx_publications.flexForm.sortby {
    publisher = LLL:EXT:myext/Resources/Private/Language/locallang.xlf:sortby.publisher
    journal   = Journal
}
```

The key is the Extbase property name used in the query ordering. LLL references are resolved automatically; plain
strings are used as-is.

## Sort direction

Controls the direction of the secondary sort field (`ASC` or `DESC`).
