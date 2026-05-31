<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Router;

\defined('_JEXEC') or die;

abstract class Rule
{
    public const TOKENS      = [];
    public const IDENTIFIERS = [];
    public const VARIABLES   =  [];
    public const KEY         = '';

    protected $tokens   = [];
    protected $language = '*';

    private $pattern = '';
    private $regex   = '';

    protected $model;

    public function __construct(string $pattern, string $language, $model)
    {
        $this->pattern  = $pattern;
        $this->language = $language;
        $this->model    = $model;
        $this->regex    = $this->createRegex($this->pattern);
        $this->tokens   = $this->extractTokens($this->pattern);
    }

    private function createRegex(string $pattern): string
    {
        $regex = preg_replace_callback('/{(\w+)}/', function ($matches) {
            return static::TOKENS[$matches[1]] ?? '([^/]+)';
        }, $pattern);

        return '#^' . $regex . '$#u';
    }

    private function extractTokens(string $pattern): array
    {
        $tokens = [];

        preg_match_all('/{(\w+)}/', $pattern, $matches, PREG_SET_ORDER);

        if (\is_array($matches) && \count($matches)) {

            $valid = array_keys(static::TOKENS);

            foreach ($matches as $match) {
                if (\in_array($match[1], $valid)) {
                    $tokens[] = $match[1];
                }
            }
        }

        return $tokens;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getTokens(bool $brackets = false): array
    {
        if ($brackets) {
            return  array_map(function ($token) {
                return '{'.$token.'}';
            }, $this->tokens);
        }

        return $this->tokens;
    }

    public function getVariables(): array
    {
        return static::VARIABLES;
    }

    public function getKey(): string
    {
        return static::KEY;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    abstract public function getItemData(array $query): array;


    abstract public function getItemKey(array $results): int;


    abstract public function getItemid(array $variables): int;
}
