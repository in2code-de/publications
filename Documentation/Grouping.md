# Grouping

Publications can be grouped in the frontend via the plugin FlexForm. The **Group by** field controls how
publications are divided into sections with headings.

## Built-in grouping options

| Value | Label          | Description                                    |
|-------|----------------|------------------------------------------------|
| none  | None           | No grouping, publications are listed flat      |
| year  | Year           | One heading per publication year               |
| type  | Type           | One heading per publication type (bibtype)     |
| year and type | Year and type | Year as primary heading, type as sub-heading |

The **Group by direction** field controls the sort direction of the primary grouping field. It has no effect
when **Group by** is set to *none*.

## Add custom groupby fields via TSConfig

Additional groupby options can be registered per page via TSConfig:

```tsconfig
tx_publications.flexForm.groupby {
    publisher = Publisher
    journal   = LLL:EXT:myext/Resources/Private/Language/locallang.xlf:groupby.journal
}
```

The key is the Extbase property name of the `Publication` model. LLL references are resolved automatically;
plain strings are used as-is.

The four built-in options (`-1`, `0`, `1`, `2`) are defined in
`EXT:publications/Configuration/TsConfig/Page/GroupByFields.tsconfig` and are always available. Custom entries
are added on top without removing the defaults.

### Available publication fields

Any scalar property of the `Publication` model can be used as a groupby field, for example:

| TSConfig key | Property        |
|--------------|-----------------|
| `publisher`  | `getPublisher()` |
| `journal`    | `getJournal()`  |
| `bibtype`    | `getBibtype()`  |
| `year`       | `getYear()`     |

> **Note:** Relation fields with dotted paths (e.g. `authors.lastName`) are not supported as custom groupby
> keys. Publications with such a groupby setting will not produce group links and the template will not render
> group headings for them. Use a custom template override (see below) for relation-based grouping.

## Frontend rendering

The built-in template renders group headings automatically for the four default options. For custom fields
the template uses dynamic property access: the heading text is taken directly from the publication's property
value.

If the value is empty, a generic *"not set"* label (`groupby.field.not-set`) is displayed instead.

### Customize the template for custom groupby fields

For advanced rendering (e.g. localized labels, custom heading levels) override the `List.html` template in
your extension and extend the `GroupTitle` section:

```typo3_typoscript
plugin.tx_publications {
    view {
        templateRootPaths.100 = EXT:myext/Resources/Private/Templates/Publications/
    }
}
```

```html
<!-- EXT:myext/Resources/Private/Templates/Publications/Publication/List.html -->

<f:section name="GroupTitle">
    <f:switch expression="{filter.groupby}">
        <!-- keep built-in cases … -->
        <f:case value="publisher">
            <f:if condition="{lastGroupTitle} != {publication.publisher}">
                <f:if condition="{publication.publisher}">
                    <f:then>
                        <h4 id="custom-group-{publication.publisher}-{data.uid}">{publication.publisher}</h4>
                    </f:then>
                    <f:else>
                        <h4><f:translate key="groupby.field.not-set"/></h4>
                    </f:else>
                </f:if>
            </f:if>
        </f:case>
    </f:switch>
</f:section>
```

## Group links for custom groupby fields

When **Show group links** is enabled in the FlexForm, anchor links to each group heading are generated
automatically. For custom scalar fields the anchor ID follows the pattern:

```
#custom-group-{value}-{contentElementUid}
```

Group links are **not** generated for dotted relation paths (e.g. `authors.lastName`).

See [GroupLinks.md](GroupLinks.md) for details on customising the link markup.