<?php
/**
 * This file is part of the teamneusta/php-cli-magedev project.
 * Copyright (c) 2017 neusta GmbH | Ein team neusta Unternehmen
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 */

use org\bovigo\vfs\vfsStream;

require_once dirname(dirname(dirname(__DIR__))) . '/phar/tasks/UpdateManifest.php';
/**
 * Class UpdateManifestTest
 */
class UpdateManifestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $root;
    /**
     * @var UpdateManifest
     */
    private $updateManifest;

    public function setUp()
    {
        parent::setUp();

        $this->root = vfsStream::setup('testDir', null, [
            'hosts.phar' => 'content',
            'manifest.json' => '[]',
            'manifest_same_release.json' => json_encode([[
                'name' => 'some_file.phar',
                'sha1' => '040f06fd774092478d450774f5ba30c5da78acc8',
                'url' => 'https://another-url.de',
                'version' => '2.5.0'
            ]]),
            'manifest_filled.json' => json_encode([[
                'name' => 'some_file.phar',
                'sha1' => 'some_sha1',
                'url' => 'https://another-url.de',
                'version' => '1.5.0'
            ]]),
            'test_release' => []
        ]);

        $testProject = new Project();
        $testProject->setName('hostsTest');

        $versionManagerMock = $this->getMockBuilder('VersionManager')
            ->setMethods(['getVersion'])
            ->getMock();

        $versionManagerMock->method('getVersion')
            ->willReturn('2.5.0');

        $this->updateManifest = new UpdateManifest($versionManagerMock);
        $this->updateManifest->setProject($testProject);
        $this->updateManifest->setBaseDir(vfsStream::url('testDir') . '/');
        $this->updateManifest->setDownloadPath('test_release');
    }

    /**
     * @test
     * @return void
     */
    public function testMainWillAddReleaseToManifestFile() {

        $this->updateManifest->setManifestPath(vfsStream::url('testDir/manifest.json'));
        $this->updateManifest->main();
        $result = [
            [
                'name' => 'hosts.phar',
                'sha1' => '040f06fd774092478d450774f5ba30c5da78acc8',
                'url' => 'http://teamneusta.github.io/php-cli-hosts/releases/hostsTest-2.5.0.phar',
                'version' => '2.5.0'
            ]
        ];

        $manifestContent = file_get_contents('vfs://testDir/manifest.json');

        self::assertEquals(
            json_encode($result),
            $manifestContent);

        self::assertSame(1, count(json_decode($manifestContent)));
    }

    /**
     * @test
     * @return void
     */
    public function testMainWillReplaceDuplicateReleasesFromManifestFile() {

        $this->updateManifest->setManifestPath(vfsStream::url('testDir/manifest_same_release.json'));
        $this->updateManifest->main();
        $result = [
            [
                'name' => 'hosts.phar',
                'sha1' => '040f06fd774092478d450774f5ba30c5da78acc8',
                'url' => 'http://teamneusta.github.io/php-cli-hosts/releases/hostsTest-2.5.0.phar',
                'version' => '2.5.0'
            ]
        ];

        $manifestContent = file_get_contents('vfs://testDir/manifest_same_release.json');

        self::assertEquals(
            json_encode($result),
            $manifestContent);

        self::assertSame(1, count(json_decode($manifestContent)));
    }

    /**
     * @test
     * @return void
     */
    public function testMainWillAppendReleasesOnExistingManifestFile() {

        $this->updateManifest->setManifestPath(vfsStream::url('testDir/manifest_filled.json'));
        $this->updateManifest->main();
        $result = [
            [
                'name' => 'some_file.phar',
                'sha1' => 'some_sha1',
                'url' => 'https://another-url.de',
                'version' => '1.5.0'
            ],
            [
                'name' => 'hosts.phar',
                'sha1' => '040f06fd774092478d450774f5ba30c5da78acc8',
                'url' => 'http://teamneusta.github.io/php-cli-hosts/releases/hostsTest-2.5.0.phar',
                'version' => '2.5.0'
            ]
        ];

        $manifestContent = file_get_contents('vfs://testDir/manifest_filled.json');

        self::assertEquals(
            json_encode($result),
            $manifestContent);

        self::assertSame(2, count(json_decode($manifestContent, true)));
    }

    /**
     * @test
     * @return void
     */
    public function testMainWillCreateManifestFileOnFirstRelease() {

        $this->updateManifest->setManifestPath(vfsStream::url('testDir/manifest_missing.json'));
        $this->updateManifest->main();
        $result = [
            [
                'name' => 'hosts.phar',
                'sha1' => '040f06fd774092478d450774f5ba30c5da78acc8',
                'url' => 'http://teamneusta.github.io/php-cli-hosts/releases/hostsTest-2.5.0.phar',
                'version' => '2.5.0'
            ]
        ];

        $manifestContent = file_get_contents('vfs://testDir/manifest_missing.json');

        self::assertEquals(
            json_encode($result),
            $manifestContent);

        self::assertSame(1, count(json_decode($manifestContent, true)));
    }
}
