# DiGA.Sax Frontend Management

## What it is

This extension provides the possibility to register for frontend user acconts on the DiGA.Sax website (frontend user management). Frontend users are ordinary TYPO3 frontend users and much of TYPO3 features are used.

Frontend users have the possibility to request access to access-restricted documents. The request will be confirmed or rejected by an adminstrative user. 

## Features

* different roles (frontend user groups) for the users
  * private, institutional, company users with indiviual access rights
  * users with global access right
  * administrative users
* administrator dashboard
  * list and edit all users
  * edit, confirm or reject user access requests
* basket feature to add mulitple documents into one request
* access restriction on frontend level depending on frontend user groups and individual access rights
* statistics function for downloads and workviews for administrators and the user itself
* save individual search requests
* scheduler tasks for jobs for the user access management 

## Requirements

This extension needs the following requirements to work properly:

* TYPO3 10.4
* [Kitodo.Presentation 4.0](https://github.com/kitodo/kitodo-presentation)
* Sitepackage [slub_web_digas](https://git.slub-dresden.de/slub-webseite/slub-web-digas)
* Extension [fe_change_pwd](https://github.com/derhansen/fe_change_pwd)
* Extension [femanager](https://github.com/in2code-de/femanager)

## Contact

If you have any questions about this extension, you may reach the developer via typo3@slub-dresden.de.
