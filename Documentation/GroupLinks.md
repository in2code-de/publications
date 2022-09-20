# Group Links
display links to grouped sections. Only active when 'Group by' selected.

## Possible groupings
`year` or `type`

## override link pre- and suffix
it is possible to override the default pre- and suffix (`[`, `]`) of section link tags via typoscript.

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
