<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Watcher;

use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Screen;
use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\Crc32ContentHash;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;
use Yosymfony\ResourceWatcher\ResourceWatcher;

final class Factory
{
    public static function create(WatchList $watchList, Screen $screen): Watcher
    {
        $resourceWatcher = new ResourceWatcher(
            new ResourceCacheMemory(), self::makeFinder($watchList), new Crc32ContentHash()
        );

        return new Watcher($resourceWatcher, $screen);
    }

    private static function makeFinder(WatchList $watchList): Finder
    {
        $finder = self::makeDefaultFinder($watchList);
        $pathsToWatch = self::extractWatchPathsFromList($watchList);

        foreach ($pathsToWatch as $watchPath) {
            self::appendFinderWithPath($finder, $watchPath);
        }

        return $finder;
    }

    private static function extractWatchPathsFromList(WatchList $watchList): array
    {
        return array_map(function ($path) {
            return new WatchPath($path);
        }, $watchList->paths());
    }

    private static function makeDefaultFinder(WatchList $watchList): Finder
    {
        return (new Finder())
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
            ->name($watchList->fileExtensions())
            ->files()
            ->notPath($watchList->ignore());
    }

    private static function appendFinderWithPath(Finder $finder, WatchPath $watchPath): void
    {
        $finder->in($watchPath->path());

        if ($watchPath->isFileOrPattern()) {
            $finder->name($watchPath->fileName());
        }
    }

}
