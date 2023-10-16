<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subsidiary extends Model
{
    protected $table = 'subsidiary';

    /**
     * Find row by locale. Ex.: pt-BR
     * @param string $locale
     * @return App\Models\Subsidiary
     */
    public static function findByLocale(string $locale)
    {
        $locale = explode('-', $locale)[1] ?? 'US';
        $subsisiary = self::where('iso_code', $locale)->first();

        return $subsisiary ?: self::find(1);
    }
}
