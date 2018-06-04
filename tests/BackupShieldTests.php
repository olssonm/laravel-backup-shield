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
		// Set parameters for testing
		$path = __DIR__ . '/resources/test-big.zip';
		$pathTest = __DIR__ . '/resources/processed.zip';

		// Make backup
		copy($path, $pathTest);

		// Manually set config
		config()->set('backup-shield.password', 'W2psdtBz9KWX49tccsr6mYwevyciTdJnJjLjtKSGkVTN1hFLH7YuaMsCBFo7AsAn');
		config()->set('backup-shield.encruption',  \Olssonm\BackupShield\Encryption::ENCRYPTION_DEFAULT);

		$data = event(new BackupZipWasCreated($pathTest));

		$this->assertEquals($pathTest, $data[0]);
	}

	/** @test **/
	public function test_encryption_protection()
	{
		// Test that the archive actually is encrypted and password protected
	}

	/** Teardown */
	public static function tearDownAfterClass()
	{
		// Delete config-file
		unlink(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/config/backup-shield.php');
		parent::tearDownAfterClass();
	}
}
