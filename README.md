moodle-local_sitestats
======================

[![Build Status](https://travis-ci.com/moodleuulm/moodle-local_sitestats.svg?branch=master)](https://travis-ci.com/moodleuulm/moodle-local_sitestats)

Moodle crawler plugin which connects to other Moodle sites to gather information about the plugins which they are running.
This is a research plugin to be used on research Moodle instances. Do not use this plugin in production environments.


Requirements
------------

This plugin requires Moodle 3.7+


Motivation for this plugin
--------------------------

On moodle.org/plugins, plugin usage statistics are available on a global level, telling plugin maintainers and interested visitors how many registered Moodle instances in the world are using a particular plugin. However, there is no breakdown into world regions available and even less there isn't any possibility to filter the official statistics to see which plugins are being used by a small set of known Moodle instances.

To overcome this limitation and to be able to produce plugin usage statistics for a local Moodle administrator community, this crawler was developed which gathers plugin usage statistics from publicly available Moodle instances.


Installation
------------

Install the plugin like any other plugin to folder
/local/sitestats

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it does not do anything to Moodle yet.

A navigation node "Site statistics" is added to the Boost navigation drawer which serves as starting point to all aspects of the plugin.


Theme support
-------------

This plugin is developed and tested on Moodle Core's Boost theme.


Plugin repositories
-------------------

The latest development version can be found on Github:
https://github.com/moodleuulm/moodle-local_sitestats


Issue reports
-------------

This plugin is provided as-is, without any warranty and without any development roadmap.

Please report any issues on Github:
https://github.com/moodleuulm/moodle-local_sitestats/issues


Copyright
---------

Ulm University
Communication and Information Centre (kiz)
Alexander Bias
