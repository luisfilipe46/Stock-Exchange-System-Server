<?php
/**
 * BasicSeed plugin data seed file.
 */

namespace App\Config\BasicSeed;

use Cake\ORM\TableRegistry;

// Write your data import statements here.
$data = [
	'devices' => [
		'_truncate' => true,
		//'_entityOptions' => [
		//	'validate' => false,
		//],
		//'_saveOptions' => [
		//	'checkRules' => false,
		//],
		'_defaults' => [
			'created' => '2016-06-06 01:00:00',
			'modified' => '2016-06-06 01:00:00'
		],
		[
			'name' => 'windows 10',
		],
		[
			'name' => 'windows 10.1',
		],
		[
			'name' => 'windows 10.2',
		],
	],
	'stocks' => [
		'_truncate' => true,
		//'_entityOptions' => [
		//	'validate' => false,
		//],
		//'_saveOptions' => [
		//	'checkRules' => false,
		//],
		'_defaults' => [
			'created' => '2016-06-06 01:00:00',
			'modified' => '2016-06-06 01:00:00'
		],
		[
			'device_id' => 1,
			'tick_name' => 'GOOG',
			'minimum' => 50.6,
			'maximum' => 850.6,
		],
		[
			'device_id' => 1,
			'tick_name' => 'AAPL',
			'minimum' => 50.6,
			'maximum' => 850.6,
		],
		[
			'device_id' => 2,
			'tick_name' => 'GOOG',
			'minimum' => 400,
			'maximum' => 850,
		],
	],
];

$this->importTables($data);
