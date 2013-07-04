<?php

App::uses('AppController', 'Controller');

/**
 * PlotsController
 *
 * PHP version 5
 *
 * @package Leaflet
 * @subpackage Leaflet.Controller
 */
class PlotsController extends  AppController {

/**
 * {@inheritdoc}
 */
	public $helpers = array('Leaflet.Leaflet');

/**
 * {@inheritdoc}
 */
	public $uses = null;

/**
 * Find.
 *
 * @param float $minLon Minimum longitude.
 * @param float $minLat Minimum latitude.
 * @param float $maxLon Maximum longitude.
 * @param float $maxLat Maximum latitude.
 * @return void
 */
	public function find($minLon, $minLat, $maxLon, $maxLat) {
		$this->layout = 'ajax';
		$data = $this->_getModel()->find('plots', compact('minLon', 'minLat', 'maxLon', 'maxLat'));
		$this->set(compact('data'));
	}

/**
 * Map.
 *
 * @return void
 */
	public function map() {

	}

/**
 * Get model to query for plots.
 *
 * @return Model
 */
	protected function _getModel() {

		$config = Configure::read('Leaflet.model');

		if (!is_array($config) && strpos($config, '.')) {
			$config = explode('.', $config);
		}

		if (is_array($config)) {
			list($plugin, $model) = $config;
		} else {
			$model = $config;
		}

		$model = ClassRegistry::init((!empty($plugin) ? $plugin . '.' : '') . $model);

		if (!$model->hasMethod('_findPlots')) {
			$model->Behaviors->attach('Leaflet.Plottable');
		}

		return $model;
	}

}
