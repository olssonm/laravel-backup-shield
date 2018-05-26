<?php

namespace Olssonm\BackupShield\Tests;

use Spatie\Backup\Tasks\Backup\Zip;
use Spatie\Backup\Events\BackupZipWasCreated;

class BackupShieldTests extends \Orchestra\Testbench\TestCase {

	public function setUp()
    {
        parent::setUp();
    }

    /**
     * Load the package
     * @return array the packages
     */
    protected function getPackageProviders($app)
    {
        return [
            \Olssonm\BackupShield\BackupShieldServiceProvider::class
        ];
    }

	/** @test */
	public function test_config_file_is_installed()
	{
		// Look for config.php
		$this->assertTrue(file_exists(__DIR__ . '/../src/config/backup-shield.php'));
	}

	/** @test */
	public function test_listener_return_data()
	{
		$path = __DIR__ . '/resources/test.zip';

		$data = event(new BackupZipWasCreated(new Zip($path)));

		$this->assertEquals($path, $data[0]);
	}

	/** Teardown */
	public static function tearDownAfterClass()
	{
		parent::tearDownAfterClass();
	}
}
