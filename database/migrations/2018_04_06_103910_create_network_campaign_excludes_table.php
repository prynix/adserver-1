<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNetworkCampaignExcludesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_campaign_excludes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->timestamps();
            $table->softDeletes();

            $table->bigInteger('campaign_id')->unsigned();
            $table->binary('name', 64); // REQ CUSTOM ALTER
            $table->binary('min', 64); // REQ CUSTOM ALTER
            $table->binary('max', 64); // REQ CUSTOM ALTER
        });

        DB::statement("ALTER TABLE network_campaign_excludes MODIFY name varbinary(64)");
        DB::statement("ALTER TABLE network_campaign_excludes MODIFY min varbinary(64)");
        DB::statement("ALTER TABLE network_campaign_excludes MODIFY max varbinary(64)");

        Schema::table('network_campaign_excludes', function (Blueprint $table) {
            $table->index(['campaign_id','name','min'], 'min');
            $table->index(['campaign_id','name','max'], 'max');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('network_campaign_excludes');
    }
}
