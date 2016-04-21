<?php

namespace AppBundle\Storage;

interface StorageInterface {

	public function has($name);

	public function set($name, $value);

	public function get($name, $default = null);

	public function all();

	public function remove($name);

	public function destroy();

}