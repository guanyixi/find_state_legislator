WordPress Plugin - Find State Legislator

Find State Legislator is a WordPress plugin that will out put a form on a page, and allow users to search legislators by zip code. 

Settings and Features
* Settings section requires Google geo api key and openstates api key.
* Put shortcode [find_state_legislator] on any page to display the form.
* It has minimal styling ready, you can customize it in your theme css file easily.
* Validation ready

This is the first WordPress plugin I've ever made from scratch. I've learned so much from this project including api, some more PHP functions, and WordPress plugin development skills. Starting from the simple part of front end development. I am getting to the really fun part of web development.


Versions

v1.0
Initial the plugin 

v1.1
Added js function to prevent image and link forced to https when site is using ssl content fix plugin.
Adjusted validations

v1.2
Changed the how the content is outputed in shortcode function. Now is returning the content instead of being echoed out. So it will display after the_content();