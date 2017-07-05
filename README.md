# WSUWP Scholarships

[![Build Status](https://travis-ci.org/washingtonstateuniversity/WSUWP-Plugin-Scholarships.svg?branch=master)](https://travis-ci.org/washingtonstateuniversity/WSUWP-Plugin-Scholarships)

A WordPress plugin for managing a collection of scholarships.

## Publishing and managing scholarships

WSUWP Scholarships provides the `scholarship` post type, which supports the following for capturing information about a scholarship:

* Taxonomies
  * University Tags
  * University Location
  * University Organization
  * Citizenship
  * Gender Identity
  * Ethnicity
  * Major
  * Grade Level
* Meta inputs
  * Minimum GPA
  * Minimum Age
  * Maximum Age
  * Application Deadline
  * Amount
  * Essay Requirement
  * State of Residence
  * Paper Application Availability
  * Online Application Availability
  * Contact Information
    * Website
    * Email
    * Phone
    * Address
  * Granting Organization
    * Name
    * About
    * Website
    * Email
    * Phone

The plugin also provides a `Scholarship Contributor` role that only grants users the permission to submit a scholarship for review.

## Shortcodes

Two shortcodes are provided by the plugin:

* `[wsuwp_search_scholarships]` - outputs a basic search form which sends submissions to the page containing the `[wsuwp_scholarships]` shortcode for results.
* `[wsuwp_scholarships]` - outputs a more robust interface for searching, browsing, sorting, and filtering scholarship results.

## Plugin settings

It's important to visit the "Scholarships" > "Settings" dashboard page to configure the plugin settings. It's also easy, considering there are only two options to set up.

* **Results Page** - the most important option, this is for selecting which page on the site contains the `[wsuwp_scholarships]` shortcode. This is used for the `href` value of the "« Scholarship Search Results" link that displays when viewing an individual scholarship, and the `action` of the search form that the `[wsuwp_search_scholarships]` shortcode outputs.
* **Active Menu Item** - this option is for selecting which page to mark as the active menu item when the search results page or an individual scholarship is being viewed.

## WordPress REST API support

The `scholarship` post type and all its associated meta is available via the WordPress REST API at the `/wp-json/wp/v2/scholarship/` endpoint.
