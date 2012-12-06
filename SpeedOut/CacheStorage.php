<?php

/**
 * Store data by ID with sharing by URL
 *
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
abstract class SpeedOut_CacheStorage {

	abstract public function store($id, $data); // TODO: add $expire argument

	abstract public function getUrl($id);

	abstract public function isStored($id);
}
