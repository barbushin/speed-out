<?php

/**
 * Store data by ID with sharing by URL
 *
 * @see https://github.com/barbushin/speed-out
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
abstract class SpeedOut_CacheStorage {

	abstract public function store($id, $data); // TODO: add $expire argument

	abstract public function getUrl($id);

	abstract public function isStored($id);
}
