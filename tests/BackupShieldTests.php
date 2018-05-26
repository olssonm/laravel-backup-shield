<?php

namespace Olssonm\BackupShield\Tests;

use Spatie\Backup\Events\BackupZipWasCreated;

use Artisan;

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
		// Run the Installation command
		Artisan::call('vendor:publish', [
			'--provider' => 'Olssonm\BackupShield\BackupShieldServiceProvider'
		]);

		$output = Artisan::output();
		$test1 = false;
		$test2 = false;

		if(strpos($output, 'Copied File') !== false) {
			$test1 = true;
		}

		if(strpos($output, '/olssonm/laravel-backup-shield/src/config/backup-shield.php') !== false) {
			$test2 = true;
		}

		$this->assertTrue($test1);
		$this->assertTrue($test2);
	}

	/** @test */
	public function test_listener_return_data()
	{
		$path = __DIR__ . '/resources/test.zip';

		$data = event(new BackupZipWasCreated($path));

		$this->assertEquals($path, $data[0]);
	}

	/** Teardown */
	public static function tearDownAfterClass()
	{
		parent::tearDownAfterClass();
	}
}
