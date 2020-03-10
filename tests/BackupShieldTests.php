<?php

namespace Olssonm\BackupShield\Tests;

use Spatie\Backup\Events\BackupZipWasCreated;
use PhpZip\ZipFile;

use Artisan;

class BackupShieldTests extends \Orchestra\Testbench\TestCase {

	public function setUp(): void
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
		$path = __DIR__ . '/resources/test.zip';
		$pathTest = __DIR__ . '/resources/processed.zip';

		// Make backup
		copy($path, $pathTest);

		// Manually set config
		config()->set('backup-shield.password', 'M79Y6aKARXa9yLrcZd3srz');
		config()->set('backup-shield.encryption',  \Olssonm\BackupShield\Encryption::ENCRYPTION_WINZIP_AES_256);

		$data = event(new BackupZipWasCreated($pathTest));

		$this->assertEquals($pathTest, $data[0]);
	}

	/** @test **/
	public function test_correct_encrypter_engine()
	{
		$path = __DIR__ . '/resources/processed.zip';

		// Use Zip-file to check attributes of the file
		$zipFile = (new ZipFile())->openFile($path);
		$zipInfo = $zipFile->getAllInfo();

		// Assume PHP 7.2 and supporter ZipArchive
		if (class_exists('ZipArchive') && in_array('setEncryptionIndex', get_class_methods('ZipArchive'))) {
			$this->assertEquals(9, $zipInfo['backup.zip']->getCompressionLevel()); // 9 = ZipArchive
		}
		// Fallback on ZipFile
		else {
			$this->assertEquals(5, $zipInfo['backup.zip']->getCompressionLevel()); // 5 = ZipFile
		}
	}

	/** @test **/
	public function test_encryption_protection()
	{
		// Test that the archive actually is encrypted and password protected
		$path = __DIR__ . '/resources/processed.zip';

		// Use Zip-file to check attributes of the file
		$zipFile = (new ZipFile())->openFile($path);
		$zipInfo = $zipFile->getAllInfo();

		$this->assertEquals(true, $zipInfo['backup.zip']->isEncrypted());
		$this->assertEquals('backup.zip', $zipInfo['backup.zip']->getName());
		$this->assertEquals(1, $zipInfo['backup.zip']->getEncryptionMethod());
	}

	/** Teardown */
	public static function tearDownAfterClass(): void
	{
		// Delete config and test-files
		$processedFile = __DIR__ . '/resources/processed.zip';
		if (file_exists($processedFile)) {
			unlink($processedFile);
		}

		$configTestFile = __DIR__ . '/../vendor/orchestra/testbench-core/laravel/config/backup-shield.php';
		if (file_exists($configTestFile)) {
			unlink($configTestFile);
		}

		$configTestFileAlt = __DIR__ . '/../vendor/orchestra/testbench-core/fixture/config/backup-shield.php';
		if (file_exists($configTestFileAlt)) {
			unlink($configTestFileAlt);
		}

		parent::tearDownAfterClass();
	}
}
