WikiMod v0.97 (for Xoops2)
==========================

WikiMod is a light-weight Wiki implementation with seamless
integration into Xoops. Inside of WikiMod, all users can easily
modify all pages and also create new ones _ad libitum_.


Installation
------------
* Copy this folder into Xoops' modules folder
* Install WikiMod through the Module Administration
* Allow the "Anonymous Users" group to access WikiMod


Usage
-----
* When you access WikiMod for the first time, you'll be asked
  to create the Wiki's homepage (usually named "WikiHome").
* Use WikiMod more or less as any other Wiki: create new pages
  by first setting a WikiLink on any page and then clicking on
  that link; edit any page by clicking on Edit at the upper right.

* As administrator, you can additionally see the history of each
  page and restore a page to a former state or fix a page's state
  (i.e. drop all older revisions).
* On the Preferences page, you can (dis)allow anonymous users to
  create new pages and modify existing ones.
* And don't forget to clean up the database from time to time:
  this removes all page revisions older than 2 months (except one).


Text Formatting Rules
---------------------
* A Wiki page consists mainly of plain text, with some added
  formatting markup. WikiMod recognizes the following markup:

= Headers go in between 'equal' signs =
<<Bold>> and {{italic}} text are marked with double brackets
* Lists start with an asterisk for each item
> Quoting is done as in email programs
  Lines starting with two spaces are printed in a monospaced font
---- on an otherwise empty line produces a horizontal bar
* An empty line marks the end of a paragraph ([[BR]] forces a line break)

* WikiLinks are written in CamelCase and automatically recognized
  (if the destination page doesn't exist, a (?) is added after the link)
* Email addresses are automatically converted to links, as well
* All other links are of the following form:
  [[URL]] or [[URL name of the link]]
* Images are inserted as follows: [[IMG URL optional alternative text]]

* Finally, there exist two special commands:
  <[PageIndex]> produces an alphabetical index of all Wiki pages
  <[RecentChanges]> lists the 10 most recently changed pages


Limitations
-----------
WikiMod doesn't offer the following features (so far):
* Access to page history and a restore option for all users
* More markup (tables, more headings, other list types, etc.)
* A separate "Comment page" feature
* InterWiki links


Revisions
---------
v0.97
* Added a Preferences page to the admin screen
* New markup: lines starting with two spaces retain their format
* Replaced the module image with a creation from Sebastian Loh (thanks)
* Fixed the "HTML-Code instead of Cancel-button" bug in the edit view

up to v0.96
* Adjusted the code more to Xoops' coding standards
* Added preview capabilities to the edit screen
* Added a "revisions count" row to the main administration screen ("Revs.")
* Changed the behavior at modification conflicts to "first edit wins"
* Minor bug fix

v0.85
* Added the possibility to clean up the database
* Minor bug fix

v0.8
* Initial release


License Notes
-------------
WikiMod is FREE SOFTWARE; you can redistribute it and/or modify it under
the terms of the GNU General Public License (version 2) as published by the
Free Software Foundation.

This module is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of merchantability or
fitness for a particular purpose. See the GNU General Public License for
more details.


Contact
-------
For comments, bug reports and suggestions write to:
Simon Bünzli <zeniko@gmx.ch>.


Copyleft © 2003 - 2004  Simon Bünzli  <zeniko@gmx.ch>
Product of zeniko's webcreations.
