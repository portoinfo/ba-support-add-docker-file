<?php

use App\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PopulateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $data = [
            ['name' => 'Português', 'iso_code' => 'pt_BR', 'terms_of_use_url' => 'https://final-helpdesk-terms.cheetah.builderall.com/termos-de-uso'],
            ['name' => 'English',   'iso_code' => 'en_US', 'terms_of_use_url' => 'https://final-helpdesk-terms.cheetah.builderall.com/terms-of-use'],
            ['name' => 'Français',  'iso_code' => 'fr_FR', 'terms_of_use_url' => 'https://final-helpdesk-terms.cheetah.builderall.com/terms-of-use'],
            ['name' => 'Español',   'iso_code' => 'es_ES', 'terms_of_use_url' => 'https://final-helpdesk-terms.cheetah.builderall.com/terms-of-use'],
            ['name' => 'Italiano',  'iso_code' => 'it_IT', 'terms_of_use_url' => 'https://final-helpdesk-terms.cheetah.builderall.com/terms-of-use'],
            ['name' => 'Português', 'iso_code' => 'pt_PT', 'terms_of_use_url' => 'https://final-helpdesk-terms.cheetah.builderall.com/terms-of-use'],
            ['name' => 'Polskie',   'iso_code' => 'pl_PL', 'terms_of_use_url' => 'https://final-helpdesk-terms.cheetah.builderall.com/terms-of-use'],
            ['name' => 'Český',     'iso_code' => 'cz_CZ', 'terms_of_use_url' => 'https://final-helpdesk-terms.cheetah.builderall.com/terms-of-use'],
        ];
        Language::insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Language::where('id', '>', 0)->delete();
    }
}



