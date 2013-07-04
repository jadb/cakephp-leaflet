<?php

App::uses('AppHelper', 'View/Helper');

/**
 * LeafletHelper
 *
 * PHP version 5
 *
 * @package Leaflet
 * @subpackage Leaflet.View.Helper
 */
class LeafletHelper extends AppHelper {

/**
 * {@inheritdoc}
 */
	public $helpers = array('Html');

/**
 * Runtime helper settings.
 *
 * @var array
 */
	public $settings = array(
		'id' => 'map',
		'assets' => array(
			'css' => '/leaflet/css/leaflet',
			'js' => '/leaflet/js/leaflet',
			'ie' => '/leaflet/css/leaflet.ie'
		),
		'blocks' => array(
			'css' => 'css',
			'js' => 'script',
			'ie' => 'ie'
		),
		'custom' =>array(
			'css' => array(),
			'js' => array(
				'/leaflet/js/map'
			),
			'ie' => array()
		),
		'map' => array(
			'id' => null,
			'attribution' => null,
			'autoload' => true,
			'clustering' => null,
			'fullscreenControl' => null,
			'lat' => array(
				'max' => null,
				'min' => null,
				'start' => null
			),
			'lng' => array(
				'max' => null,
				'min' => null,
				'start' => null
			),
			'plotting' => false,
			'provider' => null,
			'zoom' => array(
				'min' => null,
				'max' => null,
				'slider' => true,
				'start' => null
			)
		),
		'plugins' => array(
		)
	);

/**
 * Plugin-specific settings.
 *
 * @var array
 */
	private $__settingsByPlugin = array(
		'fullscreen' => array('fullscreenControl' => true),
		'markercluster' => array('clustering' => true),
		'zoomslider' => array('zoom' => array('slider' => false))
	);

/**
 * {@inheritdoc}
 */
	public function afterRender() {
		$assets = $this->settings['assets'];
		$blocks = $this->settings['blocks'];

		// load core css/js assets
		$this->Html->css($assets['css'], null, array('block' => $blocks['css']));
		$this->Html->script($assets['js'], array('block' => $blocks['js']));
		$this->Html->css($assets['ie'], null, array('block' => $blocks['ie']));

		// include plugins' css/js assets
		foreach ($this->settings['plugins'] as $plugin => $path) {
			// auto-load default assets
			if (!is_array($path)) {
				if (is_numeric($plugin)) {
					$plugin = $path;
				}

				$pluginPath = DS . implode(DS, array('leaflet', 'plugins', $plugin, $plugin));

				$path = array(
					'css' => $pluginPath,
					'js' => $pluginPath,
				);

				// include IE-specific stylesheet if it exists
				if (is_file(CakePlugin::path('Leaflet') . 'webroot' . DS . 'plugins' . DS . $plugin . DS . $plugin . '.ie.css')) {
					$path['ie'] = $pluginPath . '.ie';
				}
			}

			if (isset($path['css'])) {
				$this->Html->css($path['css'], null, array('block' => $blocks['css']));
			}

			if (isset($path['js'])) {
				$this->Html->script($path['js'], array('block' => $blocks['js']));
			}

			if (isset($path['ie'])) {
				$this->Html->css($path['ie'], null, array('block' => $blocks['ie']));
			}

			if (!empty($this->__settingsByPlugin[$plugin])) {
				$this->settings['map'] = Hash::merge($this->settings['map'], $this->__settingsByPlugin[$plugin]);
			}
		}

		$scriptBlock = sprintf('var mapSettings = %s;', json_encode(Hash::filter($this->settings['map'])));
		$this->Html->scriptBlock($scriptBlock, array('block' => $blocks['js']));

		// load custom css/js assets
		foreach ($this->settings['custom'] as $type => $includes) {
			if (empty($includes)) {
				continue;
			}

			foreach ((array) $includes as $include) {
				if (in_array($type, array('css', 'ie'))) {
					$this->Html->css($include, null, array('block' => $blocks[$type]));
					continue;
				}
				$this->Html->script($include, array('block' => $blocks['js']));
			}
		}
	}

/**
 * Create map's DIV element.
 *
 * @return string HTML for map's DIV element.
 */
	public function map() {
		return $this->Html->div(false, false, array('id' => $this->settings['id']));
	}

}
