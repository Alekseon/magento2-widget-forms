# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
### Fixed
### Added
### Removed

## [102.1.6] - 2023-02-27
### Fixed
- fix for backward compatibility

## [102.1.5] - 2022-11-22
### Added
- class "widget-form-fields-container" for div container

## [102.1.4] - 2022-11-19
### Changed
- modifications for native magento captcha
### Fixed
- decreased gap between form and submit button

## [102.1.3] - 2022-10-27
### Added
- email validation for logged in customers
- subsribe to newsletter as customer if logged in 
### Fixed
- fix for install data

## [102.1.2] - 2022-10-22
### Changed
- change widget block parameter identifier to form_identifier

## [102.1.1] - 2022-10-22
### Removed
- removed FormSubmitFailureMessage attribute
### Added
- added FormSubmitSuccessTitle attribute
### Changed
- changes in displaying success/error messages on form submit

## [102.1.0] - 2022-10-22
### Fixed
- fix infinite loading on submit where returned server error
## Added
- current product default value
- customer email default value
- hidden text input type block
- form identifier parameter for widget
- create getter methods for success and failure message
- current cms page default value provider
- default text defaul value provider

## [102.0.23]
### Fixed
- fix for warning in isSelected method for Select Field
- fix validation for newsletter email
### Changed
- TextFormAttribute source extends from CustomFormBuilder module

## [102.0.22] - 2022-10-06
### Fixed
- compatibility to attribute default values

## [102.0.21] - 2022-10-05
### Added
- compatibility to attribute default values

## [102.0.19] - 2022-10-04
### Changed
- changed composer requirements
- modification for input validations

## [102.0.18] - 2022-09-27
### Added
- images input
- max length for textarea

## [102.0.17] - 2022-09-02
### Added
- new template checkbox_vertical for multiselected checkbox  

## [102.0.16] - 2022-09-02
### Added
- allow to set tempate on frontend block by di.xml

## [102.0.15] - 2022-08-04
### Fixed
- fixed Magento 2.4.4 issue with closing the short tag in knockout templates 

## [102.0.14] - 2022-07-07
### Fixed
- form_key is now passed to Form from frontend

## [102.0.13] - 2022-07-07
### Added
- added input=date field to frontend

## [102.0.12] - 2022-06-15
### Fixed
- changed web template to render title and description as html

## [102.0.11] - 2022-06-14
### Fixed
- template rendering checkbox input when it is boolean type

## [102.0.10] - 2022-06-01
### Added
- added css className to the form wrapper element

## [102.0.9] - 2022-05-31
### Added
- added form submit failure message
### Changed
- form submit success message is now wysiwyg field
### Fixed
- some typos

## [102.0.8] - 2022-05-02
### Added
- checkbox intup block type for multiselect
- subscribe to newsetter
### Changed
- Removed widget forms from menu. Now its done by CustomFormsBuilder module

## [102.0.7] - 2022-04-14
### Fixed
- fix for widgets with non existing forms
- fix for non validated items in admin menu, for example with too long title

## [102.0.5] - 2022-04-01
### Added
- multiselect input

## [102.0.4] - 2022-04-01
### Added
- require for checkbox input

## [102.0.3]
### Changed
- rewrited widget forms to ui component on frontend
- changes to be campatible for recaptcha module
### Added
- empty option for select input
- log error during form submition
- form input types options (checkbox and radio buttons)

## [102.0.2] - 2020-10-15
### Added
- Added compatibility with Magento 2.4.0

## [101.0.0] - 2020-04-27
### Added
- init

