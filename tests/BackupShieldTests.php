<?php

namespace Olssonm\BackupShield\Tests;

use Spatie\Backup\Events\BackupZipWasCreated;

use Artisan;
use Exception;
use ZipArchive;

class BackupShieldTests extends \Orchestra\Testbench\TestCase {

	protected static $password = 'M79Y6aKARXa9yLrcZd3srz';

	public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Load the package
     *
     * @return array the packages
     */
    protected function getPackageProviders($app)
    {
        return [
            \Olssonm\BackupShield\BackupShieldServiceProvider::class
        ];
    }

	/** @test */
	public function testConfigFileIsInstalled()
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

	/**
	 * @depends testConfigFileIsInstalled
	 */
	public function testListenerReturnData()
	{
		// Set parameters for testing
		$path = __DIR__ . '/resources/test.zip';
		$pathTest = __DIR__ . '/resources/processed.zip';

		// Make file ready for testing
		copy($path, $pathTest);

		// Manually set config
		config()->set('backup-shield.password', self::$password);
		config()->set('backup-shield.encryption',  \Olssonm\BackupShield\Encryption::ENCRYPTION_WINZIP_AES_256);

		// Fire event
		$data = event(new BackupZipWasCreated($pathTest));

		$this->assertEquals($pathTest, $data[0]);
	}

	/**
	 * @depends testListenerReturnData
	 */
	public function testCorrectEncrypterEngine()
	{
		$path = __DIR__ . '/resources/processed.zip';

		$stat = null;
		$zip = new ZipArchive;

		if ($zip->open($path, ZipArchive::RDONLY) === true) {
			$stat = $zip->statIndex(0);
			$zip->close();
		} else {
			throw Exception('Could not open .zip-file');
		}
		
		$this->assertEquals('backup.zip', $stat['name']);
		$this->assertEquals(259, $stat['encryption_method']); // AES 256
		$this->assertEquals(8, $stat['comp_method']);
	}

	/**
	 * @depends testListenerReturnData
	 */
	public function testCorrectPasswordProtection()
	{
		$path = __DIR__ . '/resources/processed.zip';

		$zip = new ZipArchive;

		if ($zip->open($path) === true) {
			$zip->setPassword(self::$password);
			$result = $zip->extractTo(__DIR__ . '/resources/extraction');
			$this->assertTrue($result);
			$zip->close();
		} else {
			throw Exception('Could not open .zip-file');
		}
	}

	/**
	 * @depends testListenerReturnData
	 */
	public function testIncorrectPasswordProtection()
	{
		$path = __DIR__ . '/resources/processed.zip';

		$zip = new ZipArchive;

		if ($zip->open($path) === true) {
			$zip->setPassword('b4dp4$$w0rd');
			$result = $zip->extractTo(__DIR__ . '/resources/extraction');
			$this->assertFalse($result);
			$zip->close();
		} else {
			throw Exception('Could not open .zip-file');
		}
	}

	public function testRetrieveEncryptionConstants()
	{
		$encryption = new \Olssonm\BackupShield\Encryption();

		$this->assertEquals('257', \Olssonm\BackupShield\Encryption::ENCRYPTION_DEFAULT);
		$this->assertEquals('257', \Olssonm\BackupShield\Encryption::ENCRYPTION_DEFAULT);
		$this->assertEquals('258', \Olssonm\BackupShield\Encryption::ENCRYPTION_WINZIP_AES_192);
		$this->assertEquals('259', \Olssonm\BackupShield\Encryption::ENCRYPTION_WINZIP_AES_256);
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

		// Delete extracted files
		array_map('unlink', glob(__DIR__ . "/resources/extraction/*.*"));
		rmdir(__DIR__ . "/resources/extraction");

		parent::tearDownAfterClass();
	}
}
