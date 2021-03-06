<?php
/**
 * This file is part of the BEAR.Package package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Context;

use BEAR\Package\Context\Provider\FilesystemCacheProvider;
use BEAR\RepositoryModule\Annotation\Storage;
use BEAR\Resource\Annotation\LogicCache;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\FileCache;
use Doctrine\Common\Cache\FilesystemCache;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class ProdModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        if (function_exists('apcu_fetch') && class_exists(ApcuCache::class)) {
            $this->installApcCache(ApcuCache::class);

            return;
        }
        if (function_exists('apc_fetch')) {
            $this->installApcCache(ApcCache::class);

            return;
        }
        $this->installFileCache();
    }

    private function installApcCache($apcClass)
    {
        $this->bind(Cache::class)->to($apcClass)->in(Scope::SINGLETON);
        $this->bind(Reader::class)->toConstructor(
            CachedReader::class,
            'reader=annotation_reader'
        );
        $this->bind(Reader::class)->annotatedWith('annotation_reader')->to(AnnotationReader::class);
        $this->bind(CacheProvider::class)->annotatedWith(Storage::class)->to($apcClass)->in(Scope::SINGLETON);
        $this->bind(Cache::class)->annotatedWith(LogicCache::class)->to($apcClass)->in(Scope::SINGLETON);
        $this->bind(Cache::class)->annotatedWith(Storage::class)->to($apcClass)->in(Scope::SINGLETON);
    }

    private function installFileCache()
    {
        $this->bind(Cache::class)->toProvider(FilesystemCacheProvider::class)->in(Scope::SINGLETON);
        $this->bind(Reader::class)->toConstructor(
            CachedReader::class,
            'reader=annotation_reader'
        );
        $this->bind(Reader::class)->annotatedWith('annotation_reader')->to(AnnotationReader::class);
        $this->bind(CacheProvider::class)->annotatedWith(Storage::class)->toProvider(FilesystemCacheProvider::class)->in(Scope::SINGLETON);
        $this->bind(Cache::class)->annotatedWith(LogicCache::class)->toProvider(FilesystemCacheProvider::class)->in(Scope::SINGLETON);
        $this->bind(Cache::class)->annotatedWith(Storage::class)->toProvider(FilesystemCacheProvider::class)->in(Scope::SINGLETON);
    }
}
