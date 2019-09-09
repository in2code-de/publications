# publications is an TYPO3 extension to list academic publications

Inspired by the - meanwhile outdated - TYPO3 bib extension



## Introduction

Target group of this extension are universities and colleges:
Show academic publications in a listview with some filter possibilities in the plugin and in frontend.
Im- and export BibTeX or XML files (Import via Backend Module, Export in Frontend) to list publications.
Easily extend this extension with own importers or own citestyles. 

At the moment we support a default and the IEEE citestyle.



## Screenshots

Example list view:
![Example listview](Documentation/Images/screenshot_frontend_listview.png "Listview")

Plugin:
![Plugin](Documentation/Images/screenshot_backend_plugin.png "Plugin")

Import module:
![Module](Documentation/Images/screenshot_backend_module.png "Module")



## Technical requirements

This extension needs minimum *TYPO3 8.7* and PHP 7.0.
At the moment it's not possible to use publications without **composer mode**! Classic mode is not supported.



## Installation

* First of all, intall the extension via composer: `composer require in2code/publications`
* Clean caches
* Add the static TypoScript of the extension to your installation root template
* Add some publication and author records to a sysfolder
* Add the publication plugin to a default page 
* That's it



## Extending publications

* Look at the [importer documentation](Documentation/Importer.md) to see how you can add own importers
* Look at the [citestyle documentation](Documentation/Citestyles.md) to see how you can add your own cite styles



## Migration from bib

If you want to migrate records from extension bib to publications, there is a Command Controller for doing this

```
./vendor/bin/typo3cms publications:migrate
```

**Note:** If you want to delete all publications records before (to minimize uid conflicts), you can use

```
# Delete all records (truncate all tables)
./vendor/bin/typo3cms publications:clean 0

# Delete all records on page with uid 123
./vendor/bin/typo3cms publications:clean 123
```



## Changelog

| Version    | Date       | State      | Description                                                                        |
| ---------- | ---------- | ---------- | ---------------------------------------------------------------------------------- |
| 1.3.0      | 2019-09-09 | Feature    | Allow multiple authors for filtering be+fe, Prefilter with extern/intern in plugin |
| 1.2.0      | 2019-09-02 | Task       | Allow table wide exclude field definition, small change for title and bibtype tca  |
| 1.1.0      | 2019-08-29 | Task       | Fix issue with staticfilecache, use individual filter cookie per ce, year bugfix   |
| 1.0.1      | 2019-08-27 | Bugfix     | Fix some small typos                                                               |
| 1.0.0      | 2019-08-27 | Task       | First stable release                                                               |
| 0.4.0      | 2019-08-26 | Task       | 4. prerelease with documentation                                                   |
| 0.3.0      | 2019-08-26 | Task       | 3. prerelease with a finalized citestyles                                          |
| 0.2.0      | 2019-08-26 | Task       | 2. prerelease with a basic IEEE citestyle                                          |
| 0.1.0      | 2019-08-21 | Task       | First prerelease with a default citestyle only                                     |



## Patrons

* <a href="https://www.uni-ulm.de" target="_blank">University of Ulm</a> as the main sponsor of this extension
* <a href="https://www.in2code.de" target="_blank" title="Wir leben TYPO3">in2code GmbH</a> as the development partner of this extension
