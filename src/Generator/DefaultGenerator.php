<?php

namespace Shahrestani\Avatar\Generator;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DefaultGenerator implements GeneratorInterface
{
    protected $name;

    public function make($name, $length = 2, $uppercase = false, $ascii = false, $rtl = false)
    {
        $this->setName($name, $ascii);

        $is_arabic = preg_match('/\p{Arabic}/u', $this->name);

        $words = new Collection(explode(' ', $this->name));

        // if name contains single word, use first N character
        if ($words->count() === 1) {
            $initial = (string) $words->first();

            if (strlen($this->name) >= $length) {
                $initial = Str::substr($this->name, 0, $length);
            }

            if($is_arabic){
                $Arabic = new \ArPHP\I18N\Arabic();
                $initial = $Arabic->utf8Glyphs($initial);
            }
        } else {
            // otherwise, use initial char from each word
            $initials = new Collection();
            $words->each(function ($word) use ($initials) {
                $initials->push(Str::substr($word, 0, 1));
            });

            $initial = $initials->slice(0, $length)->implode('');

            if($is_arabic){
                $initial = collect(mb_str_split($initial))->reverse()->implode('');
            }
        }

        if ($uppercase && !$is_arabic) {
            $initial = strtoupper($initial);
        }

        if ($rtl) {
//            $initial = collect(mb_str_split($initial))->reverse()->implode('');
        }

        return $initial;
    }

    private function setName($name, $ascii)
    {
        if (is_array($name)) {
            throw new \InvalidArgumentException(
                'Passed value cannot be an array'
            );
        } elseif (is_object($name) && !method_exists($name, '__toString')) {
            throw new \InvalidArgumentException(
                'Passed object must have a __toString method'
            );
        }

        if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
            // turn bayu.hendra@gmail.com into "Bayu Hendra"
            $name = str_replace('.', ' ', Str::before($name, '@'));
        }

        if ($ascii) {
            $name = Str::ascii($name);
        }

        $this->name = $name;
    }
}
