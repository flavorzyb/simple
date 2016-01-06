<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/12/24
 * Time: 下午11:59
 */

namespace Simple\Translation;


class Lang
{
    /**
     * the base path
     *
     * @var string
     */
    protected $path = "";
    /**
     * The default locale being used by the translator.
     *
     * @var string
     */
    protected $locale;

    /**
     * The fallback locale used by the translator.
     *
     * @var string
     */
    protected $fallback;

    /**
     * The array of loaded translation groups.
     *
     * @var array
     */
    protected $loaded = array();

    /**
     * Lang constructor.
     * @param string $locale
     * @param string $path
     */
    public function __construct($locale, $path = "")
    {
        $this->locale = $locale;
        $this->path = trim($path);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * parse key to [group, item]
     * @param string $key
     * @return array
     */
    protected function parseKey($key) {
        $key = trim($key);
        $data = explode('.', $key);
        $group = trim($data[0]);
        if (1 == count($data)) {
            return [$group, null];
        }

        return [$group, $data[1]];
    }

    /**
     * parse locale
     * @param string $locale
     * @return array
     */
    protected function parseLocale($locale) {
        if (null == $locale) {
            return [$this->locale, $this->fallback];
        }

        return [$locale, $this->fallback];
    }

    /**
     * get string by key and locale
     *
     * @param string $key
     * @param string $locale
     * @return string
     */
    public function get($key, $locale = null) {
        $segments = $this->parseKey($key);
        $group = $segments[0];
        $item = $segments[1];

        if ("" == $group) {
            return "";
        }

        $localeArray = $this->parseLocale($locale);
        foreach ($localeArray as $locale) {
            $this->load($group, $locale);

            if (isset($this->loaded[$group][$locale])) {
                if (null == $item) {
                    return $this->loaded[$group][$locale];
                } elseif (isset($this->loaded[$group][$locale][$item])) {
                    return $this->loaded[$group][$locale][$item];
                }
            }
        }

        return "";
    }

    protected function load($group, $locale) {
        if ($this->isLoaded($group, $locale)) return ;
        $path = $this->path . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $group . ".php";
        if (is_file($path)) {
            $this->loaded[$group][$locale] = include $path;
        }
    }

    protected function isLoaded($group, $locale) {
        return isset($this->loaded[$group][$locale]);
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function locale()
    {
        return $this->getLocale();
    }

    /**
     * Get the default locale being used.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the default locale.
     *
     * @param  string  $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get the fallback locale being used.
     *
     * @return string
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Set the fallback locale being used.
     *
     * @param  string  $fallback
     * @return void
     */
    public function setFallback($fallback)
    {
        $this->fallback = $fallback;
    }
}
