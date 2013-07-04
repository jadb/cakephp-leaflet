# CakePHP Leaflet Plugin

To minimize the required code to be written when using [Leaflet JS][1] (0.5+) with [CakePHP][2] (2.x+) for
mapping features, I decided to put everything into a plugin that can handle most general types of mapping
needs. It works for simple maps, marker(s) plotting and clustering.

It comes with the following Leaflet plugins built-in and can be very easily extended:

- [Leaflet.fullscreen][3]
- [Leaflet.markercluster][4]
- [Leaflet.zoomslider][5]

## Install

### Composer package

First, add this plugin as a requirement to your `composer.json`:

	{
		"require": {
			"cakephp/leaflet": "*"
		}
	}

And then update:

	php composer.phar update

That's it! You should now be ready to start configuring your channels.

### Submodule

	$ cd /app
	$ git submodule add git://github.com/jadb/cakephp-leaflet.git Plugin/Leaflet

### Clone

	$ cd /app/Plugin
	$ git clone git://github.com/jadb/cakephp-leaflet.git

## Configuration

You need to enable the plugin your `app/Config/bootstrap.php` file:

	CakePlugin::load('Leaflet');

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

In order to use this plugin for plotting markers on a map, you will need a 'plottable' model. A model that has
`longitude` and `latitude` columns. By default, the columns' are named `lon` and `lat` respectively, but this
can be customized when attaching the `PlottableBehavior` to your model. Define your 'plottable' model by doing:

	Configure::write('Leaflet.model', 'YourPlottableModel');

## Usage

@todo write some usage example(s)

## Patches & Features

* Fork
* Mod, fix
* Test - this is important, so it's not unintentionally broken
* Commit - do not mess with license, todo, version, etc. (if you do change any, bump them into commits of their own that I can ignore when I pull)
* Pull request - bonus point for topic branches

## Bugs & Feedback

http://github.com/jadb/cakephp-leaflet/issues

## License

Copyright 2013, [Jad Bitar](http://jadb.io)

Licensed under [The MIT License](http://www.opensource.org/licenses/mit-license.php)<br/>
Redistributions of files must retain the above copyright notice.

[1]:http://leafletjs.com
[2]:http://cakephp.org
[3]:https://github.com/brunob/leaflet.fullscreen
[4]:https://github.com/Leaflet/Leaflet.markercluster
[5]:https://github.com/kartena/Leaflet.zoomslider
