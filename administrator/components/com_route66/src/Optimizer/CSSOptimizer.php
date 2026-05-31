<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Optimizer;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use MatthiasMullie\Minify;

class CSSOptimizer
{
    protected $processor;
    protected array $processed;
    protected $cache;
    protected string $rootAbsolute;
    protected string $rootRelative;

    public function __construct()
    {
        $this->processor    = $processor = new Minify\CSS();
        $this->processed    = [];
        $this->rootAbsolute = Uri::root(false);
        $this->rootRelative = Uri::root(true);

        $this->cache = Factory::getCache('com_route66', 'output');
        $this->cache->setCaching(true);
        $this->cache->setLifeTime(180);
    }

    public function add(\DomElement $style): bool
    {
        if ($style->tagName !== 'style' && $style->tagName !== 'link') {
            return false;
        }

        if ($style->parentNode->tagName === 'template') {
            return false;
        }

        if ($style->parentNode->tagName === 'noscript') {
            return false;
        }

        if ($style->tagName == 'style') {
            return $this->addStyle($style->nodeValue);
        }

        return $this->addLink($style->getAttribute('href'));
    }


    public function addLink(string $href): bool
    {
        if (!$href || $this->isExternal($href) || $this->isScript($href)) {
            return false;
        }

        $cacheId = $this->getCacheId($href);

        if (!$this->cache->contains($cacheId)) {

            $filepath = $this->getFilePath($href);
            $realFile = JPATH_SITE . '/' . $filepath;

            if (!is_file($realFile)) {
                return false;
            }

            $realpath = realpath($realFile);

            if (!$realpath) {
                return false;
            }

            $buffer = file_get_contents($realpath);

            if ($buffer === false) {
                return false;
            }

            $buffer = $this->prepareStyle($buffer, $href);

            $this->cache->store($buffer, $cacheId);
        }

        $this->processor->add($this->cache->get($cacheId));
        $this->processed[] = $cacheId;

        return true;
    }

    public function addStyle(string $buffer, string $path = ''): bool
    {
        $cacheId = $this->getCacheId($buffer);

        if (!$this->cache->contains($cacheId)) {
            $buffer = $this->prepareStyle($buffer, $path);
            $this->cache->store($buffer, $cacheId);
        }

        $this->processor->add($this->cache->get($cacheId));
        $this->processed[] = $cacheId;

        return true;
    }

    protected function prepareStyle(string $buffer, string $path = '')
    {
        $basepath = $path ? '/' . \dirname($this->getFilePath($path)) : '/';

        // Handle @import rules recursively
        $imports = $this->findImports($buffer);

        foreach ($imports as $import) {
            $importPath = $import['path'] ?? '';

            if (!$importPath || $this->isExternal($importPath)) {
                continue;
            }

            $replacement = $this->relativeToAbsolute($importPath, $basepath);

            if ($replacement) {
                $buffer = str_replace($import[0], $replacement, $buffer);
            }
        }

        // Fix url(...) paths inside the CSS with regex callback to avoid duplicate replacements when one string contains an other
        $buffer = preg_replace_callback('/url\((["\']?)(.*?)\1\)/i', function ($matches) use ($basepath) {
            $url = $matches[2];

            if ($this->isExternal($url) || str_starts_with($url, 'data:')) {
                return $matches[0]; // leave as is
            }

            $replacement = $this->relativeToAbsolute($url, $basepath);
            return "url('{$replacement}')";
        }, $buffer);

        return $buffer;
    }

    public function combine(): string
    {
        if (!\count($this->processed)) {
            return '';
        }

        $cacheId = $this->getCacheId(implode('', $this->processed));
        if (!$this->cache->contains($cacheId)) {
            $this->cache->store($this->processor->minify(), $cacheId);
        }

        return $this->cache->get($cacheId);
    }

    protected function findImports(string $buffer): array
    {
        $expressions = [
            '/@import\s+url\((?P<quotes>["\']?)(?P<path>.+?)(?P=quotes)\)\s*(?P<media>[^;]*)\s*;?/i',
            '/@import\s+(?P<quotes>["\'])(?P<path>.+?)(?P=quotes)\s*(?P<media>[^;]*)\s*;?/i',
        ];

        $matches = [];

        foreach ($expressions as $expression) {
            if (preg_match_all($expression, $buffer, $newMatches, PREG_SET_ORDER)) {
                $matches = array_merge($matches, $newMatches);
            }
        }

        return $matches;
    }

    protected function findUrls(string $buffer): array
    {
        preg_match_all('/url\((["\']?)(.*?)\1\)/i', $buffer, $matches);

        return $matches[2] ?? [];
    }

    protected function relativeToAbsolute(string $url, string $basepath): ?string
    {
        $url = trim($url, '\'"');

        if (str_starts_with($url, '/') || str_starts_with($url, 'http') || str_starts_with($url, 'data:')) {
            return $url;
        }

        $baseParts = explode('/', trim($basepath, '/'));
        $urlParts  = explode('/', $url);

        foreach ($urlParts as $part) {
            if ($part === '..') {
                array_pop($baseParts);
            } elseif ($part !== '.' && $part !== '') {
                $baseParts[] = $part;
            }
        }

        return '/' . implode('/', $baseParts);
    }

    protected function isExternal(string $href): bool
    {
        return str_starts_with($href, '//') || (str_starts_with($href, 'http') && !str_starts_with($href, $this->rootAbsolute));
    }

    protected function isScript(string $href): bool
    {
        $path = parse_url($href, PHP_URL_PATH);

        if (!$path) {
            return false;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return strtolower($extension) === 'php';
    }

    protected function getFilePath(string $href): string
    {
        if (str_starts_with($href, $this->rootAbsolute)) {
            $href = str_replace($this->rootAbsolute, '', $href);
        } elseif ($this->rootRelative && str_starts_with($href, $this->rootRelative)) {
            $href = str_replace($this->rootRelative . '/', '', $href);
        }

        $filepath = parse_url($href, PHP_URL_PATH);

        return ltrim($filepath, '/');
    }

    protected function getCacheId(string $input): string
    {
        return md5($input);
    }
}
