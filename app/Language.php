<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['name', 'terms_of_use_url', 'iso_code'];

    /**
     * Returns language
     * @param mixed $ref
     * @param bool $use_default
     * @return Language|null
     */
    public static function getRow($ref, bool $use_default = true)
    {
        $language = is_int($ref) ? self::find($ref) : self::where('iso_code', $ref)->first();

        if (!$language && $use_default) 
        {
            $language = self::where('iso_code', 'en_US')->first();
        }

        return $language;
    }
}
