<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/18
 * Time: 上午9:45
 */

namespace Simple\Environment;

use InvalidArgumentException;

class Loader
{
    /**
     * The file path.
     *
     * @var string
     */
    protected $filePath;
    /**
     * Are we immutable?
     *
     * @var bool
     */
    protected $immutable = false;
    /**
     * Create a new loader instance.
     *
     * @param string $filePath
     * @param bool   $immutable
     */
    public function __construct($filePath, $immutable = false)
    {
        $this->filePath = $filePath;
        $this->immutable = $immutable;
    }
    /**
     * Load `.env` file in given directory.
     *
     * @return array
     */
    public function load()
    {
        $this->ensureFileIsReadable();
        $filePath = $this->filePath;
        $lines = $this->readLinesFromFile($filePath);
        foreach ($lines as $line) {
            if ($this->isComment($line)) {
                continue;
            }
            if ($this->looksLikeSetter($line)) {
                $this->setEnvironmentVariable($line);
            }
        }
        return $lines;
    }

    /**
     * Ensures the given filePath is readable.
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureFileIsReadable()
    {
        $filePath = $this->filePath;
        if (!is_readable($filePath) || !is_file($filePath)) {
            throw new InvalidArgumentException(sprintf(
                'Dotenv: Environment file .env not found or not readable. '.
                'Create file with your environment settings at %s',
                $filePath
            ));
        }
    }
    /**
     * Normalise the given environment variable.
     *
     * Takes value as passed in by developer and:
     * - ensures we're dealing with a separate name and value, breaking apart the name string if needed
     * - cleaning the value of quotes
     * - cleaning the name of quotes
     * - resolving nested variables
     *
     * @param $name
     * @param $value
     *
     * @return array
     */
    protected function normaliseEnvironmentVariable($name, $value)
    {
        list($name, $value) = $this->splitCompoundStringIntoParts($name, $value);
        return array($name, $value);
    }

    /**
     * Read lines from the file, auto detecting line endings.
     *
     * @param string $filePath
     *
     * @return array
     */
    protected function readLinesFromFile($filePath)
    {
        // Read file into an array of lines with auto-detected line endings
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autodetect);
        return $lines;
    }
    /**
     * Determine if the line in the file is a comment, e.g. begins with a #.
     *
     * @param string $line
     *
     * @return bool
     */
    protected function isComment($line)
    {
        return strpos(trim($line), '#') === 0;
    }
    /**
     * Determine if the given line looks like it's setting a variable.
     *
     * @param string $line
     *
     * @return bool
     */
    protected function looksLikeSetter($line)
    {
        return strpos($line, '=') !== false;
    }
    /**
     * Split the compound string into parts.
     *
     * If the `$name` contains an `=` sign, then we split it into 2 parts, a `name` & `value`
     * disregarding the `$value` passed in.
     *
     * @param string $name
     * @param string $value
     *
     * @return array
     */
    protected function splitCompoundStringIntoParts($name, $value)
    {
        if (strpos($name, '=') !== false) {
            list($name, $value) = array_map('trim', explode('=', $name, 2));
        }
        return array($name, $value);
    }

    /**
     * Search the different places for environment variables and return first value found.
     *
     * @param string $name
     *
     * @return string
     */
    public function getEnvironmentVariable($name)
    {
        switch (true) {
            case array_key_exists($name, $_ENV):
                return $_ENV[$name];
            case array_key_exists($name, $_SERVER):
                return $_SERVER[$name];
            default:
                $value = getenv($name);
                return $value === false ? null : $value; // switch getenv default to null
        }
    }
    /**
     * Set an environment variable.
     *
     * This is done using:
     * - putenv
     * - $_ENV
     * - $_SERVER.
     *
     * The environment variable value is stripped of single and double quotes.
     *
     * @param $name
     * @param string|null $value
     *
     * @return void
     */
    public function setEnvironmentVariable($name, $value = null)
    {
        list($name, $value) = $this->normaliseEnvironmentVariable($name, $value);

        // Don't overwrite existing environment variables if we're immutable
        // Ruby's dotenv does this with `ENV[key] ||= value`.
        if ($this->immutable === true && !is_null($this->getEnvironmentVariable($name))) {
            return;
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        putenv("$name=$value");
    }
}
