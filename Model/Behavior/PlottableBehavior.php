<?php

App::uses('ModelBehavior', 'Model');

/**
 * PlottableBehavior
 *
 * PHP version 5
 *
 * @package Leaflet
 * @subpackage Leaflet.Model.Behavior
 */
class PlottableBehavior extends ModelBehavior {

/**
 * {@inheritdoc}
 */
	public $mapMethods = array(
		'/\b_findPlot\b/' => '_findPlot',
		'/\b_findPlots\b/' => '_findPlots'
	);

/**
 * {@inheritdoc}
 */
	public $settings = array();

	protected $_defaults = array(
		'longitude' => 'lon',
		'latitude' => 'lat',
	);

/**
 * {@inheritdoc}
 */
	public function setup(Model $model, $settings = array()) {
		$this->settings[$model->alias] = Hash::merge($this->_defaults, $model->buildQuery('all'), $settings);
		foreach ($this->_defaults as $field) {
			$this->settings[$model->alias]['fields'][] = $model->alias . '.' . $field;
		}
		$model->findMethods['plot'] = true;
		$model->findMethods['plots'] = true;
	}

/**
 * Map and reduce dataset to work with Leaflet markers.
 *
 * @param Model $model Query model.
 * @param array $data Plots data.
 * @return array
 */
	public function mapReducePlotData(Model $model, $data) {
		if (empty($data)) {
			return $data;
		}

		if (method_exists($model, 'mapReducePlotData')) {
			return $model->mapReducePlotData($data);
		}

		return Hash::extract($data, "{n}.$model->alias");
	}

/**
 * Custom method to first plot given minimums and maximums for longitude and latitude.
 *
 * @param Model $model Model to query.
 * @param string $func
 * @param string $state Either "before" or "after"
 * @param array $query
 * @param array $result
 * @return array
 */
	public function _findPlot(Model $model, $func, $state, $query, $result = array()) {
		if ('after' === $state) {
			return $result;
		}

		$query['limit'] = 1;
		return $this->_findPlots($model, $func, $state, $query, $result);
	}

/**
 * Custom method to find all plots given minimums and maximums for longitude and latitude.
 *
 * @param Model $model Model to query.
 * @param string $func
 * @param string $state Either "before" or "after"
 * @param array $query
 * @param array $result
 * @return array
 */
	public function _findPlots(Model $model, $func, $state, $query, $result = array()) {
		if ('after' === $state) {
			if (count($result) == 1) {
				return $this->mapReducePlotData($model, array_pop($result));
			}
			return $this->mapReducePlotData($model, $result);
		}

		$query = Hash::merge(Hash::filter($this->settings[$model->alias]), Hash::filter($query));
		if (!isset($query['order'])) {
			$query['order'] = null;
		}

		foreach (array('minLon', 'minLat', 'maxLon', 'maxLat') as $key) {
			if (!isset($query[$key])) {
				throw new RuntimeException();
			}

			$field = $query['longitude'];
			if (false !== strpos($key, 'Lat')) {
				$field = $query['latitude'];
			}

			$operator = '>=';
			if (false !== strpos($key, 'max')) {
				$operator = '<=';
			}

			$query['conditions']["$model->alias.$field $operator"] = $query[$key];
			unset($query[$key]);
		}

		unset($query['longitude'], $query['latitude']);

		return $query;
	}

}
