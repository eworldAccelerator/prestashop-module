<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 18/09/2015
 * Time: 17:10
 */

class eworldAcceleratorPrestashopHandler {

	/** @var string $directory */
	private $directory;
	/** @var EworldAcceleratorAPI $api */
	private $api;

	function __construct($directory) {
		$this->directory = $directory;
	}

	/**
	 * @return bool
	 */
	private function getAPI() {
		if (is_object($this->api)) {
			return true;
		}
		else {
			if (file_exists($this->directory) && file_exists($this->directory . 'run_cache.php')) {
				require_once $this->directory . 'inc/EworldAcceleratorAPI.php';

				$this->api = new EworldAcceleratorAPI($this->directory);

				return true;
			}
		}
		return false;
	}

	/**
	 * @param string $permalink
	 * @return bool
	 */
	public function deleteURL($permalink) {
		if ($this->getAPI()) {
			$this->api->deleteURL($permalink);
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function deleteAllCache() {
		if ($this->getAPI()) {
			$this->api->deleteAllCachedFiles();
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function garbageCollector() {
		if ($this->getAPI()) {
			$this->api->gc();
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function purgeCDN() {
		if ($this->getAPI()) {
			$this->api->cdnPurge();
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function isCdnActive() {
		if ($this->getAPI()) {
			return $this->api->isCdnActive();
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function getVersion() {
		if ($this->getAPI()) {
			return $this->api->getVersion();
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function isThereSystemUpdate() {
		if ($this->getAPI()) {
			return $this->api->isThereSystemUpdate();
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getSystemUpdateLink() {
		if ($this->getAPI()) {
			return $this->api->getSystemUpdateLink();
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getDashboardURL() {
		if ($this->getAPI()) {
			return str_replace('//', '/', $this->api->getDashboardURL());
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getConfigurationURL() {
		if ($this->getAPI()) {
			return str_replace('//', '/', $this->api->getConfigurationURL());
		}
		return false;
	}
}