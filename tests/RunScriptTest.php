<?php declare(strict_types=1);

namespace tests;

use tests\Helper\Filesystem;
use tests\Helper\WatcherRunner;
use tests\Helper\WatcherTestCase;

final class RunScriptTest extends WatcherTestCase
{
    /** @test */
    public function it_runs_a_php_script(): void
    {
        $scriptToRun = Filesystem::createHelloWorldPHPFile();
        $watcher = (new WatcherRunner)->run($scriptToRun);

        sleep(1);
        $output = $watcher->getOutput();

        $this->assertStringContainsString("starting `php $scriptToRun`", $output);
        $this->assertStringContainsString('Hello, world', $output);
    }
}
