<?php

namespace FA\Tests\Composer\Script;

use Composer\Script\Event;
use Composer\Util\Filesystem;
use FA\Composer\Script\SQLite;

/**
 * @group composer
 */
class SQLiteTest extends ComposerScriptTestCase
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * Path to db
     *
     * @var string Path to db
     */
    protected $dbFile;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    protected function setUp()
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
        $this->dbFile = $this->config['database'];
        $this->filesystem->remove($this->dbFile);

        // Create Composer config
        $this->composerConfig->merge(
            array(
                'config' => array(
                    'vendor-dir' => APPLICATION_PATH . '/vendor'
                ),
            )
        );
    }

    protected function tearDown()
    {
        $this->filesystem->remove($this->dbFile);
        parent::tearDown();
    }

    public function testPrepareNotExists()
    {
        $this->assertFalse(file_exists($this->dbFile));

        $output = array(
            'Reviewing your Flaming Archer database . . .',
            'Creating new database . . .',
            "Done!"
        );

        $this->configureExpectations($output, 'testing');

        SQLite::prepare($this->event);
        $this->assertTrue(file_exists($this->dbFile));
    }

    public function testPrepareExists()
    {
        $config = $this->config;
        $this->setUpDbOnFilesystem();

        $output = array(
            'Reviewing your Flaming Archer database . . .',
            'Database found.'
        );

        $this->configureExpectations($output, 'testing');

        SQLite::prepare($this->event);
    }

    public function testPDOConnectionIssueThrowsException()
    {
        $this->setExpectedException('\PDOException');

        $output = array(
            'Reviewing your Flaming Archer database . . .',
            'Creating new database . . .',
        );

        $this->configureExpectations($output, 'testing-bad-dsn');

        SQLite::prepare($this->event);
    }

    public function testGetConfigEnvironmentDevEnvironment()
    {
        $event = new Event('post-install-cmd', $this->composerMock, $this->consoleIO, true);

        $this->composerMock->expects($this->once())
                ->method('getPackage')
                ->will($this->returnValue($this->package));

        $this->package->expects($this->once())
            ->method('getExtra')
            ->will($this->returnValue(array()));

        $this->assertEquals('development', SQLite::getConfigEnvironment($event));
    }

    public function testGetConfigEnvironmentProdEnvironment()
    {
        $event = new Event('post-install-cmd', $this->composerMock, $this->consoleIO, false);

        $this->composerMock->expects($this->once())
                ->method('getPackage')
                ->will($this->returnValue($this->package));

        $this->package->expects($this->once())
            ->method('getExtra')
            ->will($this->returnValue(array()));

        $this->assertEquals('production', SQLite::getConfigEnvironment($event));
    }

    protected function configureExpectations(array $output, $configEnv)
    {
        foreach ($output as $index => $message) {
            $this->outputMock->expects($this->at($index))
                    ->method('write')
                    ->with($this->equalTo($message), $this->equalTo(true));
        }

        $this->composerMock->expects($this->once())
                ->method('getConfig')
                ->will($this->returnValue($this->composerConfig));

        $this->composerMock->expects($this->once())
                ->method('getPackage')
                ->will($this->returnValue($this->package));

        $this->package->expects($this->once())
            ->method('getExtra')
            ->will($this->returnValue(array('configEnvironment' => $configEnv)));
    }
}
