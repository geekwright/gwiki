FBComment Module Version 1.0 for Xoops/ImpressCMS

Purpose
=======
FBComment adds Facebook comments and like button functions 
to your CMS with little or no template and/or coding changes. 
A system to customize the Open Graph Protocol data exposed 
to Facebook by your site is also included.


Installation
============
Copy fbcomment directory to the modules directory of your 
site, then install like any other module in your site's 
administration area.

The module administration pages include a quick start 
guide for configuration

Usage
=====
This module does not have a real main page at this time. It 
consists only of blocks and the neccessary scripts to support 
those blocks. Attempts to use the fbcomment/index.php will
result in an empty page. It is recommended to turn it off
in the main menu in the CMS modules administration.

Group access permissions to the fbcomment module are required 
to use the blocks, above and beyond the block display permission. 
Interacting with the blocks records the url, action and datetime
via AJAX style calls to scripts in the fbcomment directory
which use the standard mainfile inclusion which adds a layer of
additional protections (such as protector) but requires access
permissions to work as intended.

Also note, in normal operation, Facebook will pull data from 
your site as the annonymous user. 

Module administration permissions enable in-block editing of 
Open Graph meta data.

Three blocks are available.

Comment     - Facebook comments
Like        - Facebook like button
Combo       - Facebook like button and comments in a single block

These blocks can be cloned as needed.

Notes
=====

This module has been tested in Xoops version 2.5.5.

This module was developed by Geekwright, LLC. Report any bugs
or issues to richard@geekwright.com
