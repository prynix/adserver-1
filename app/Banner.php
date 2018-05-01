<?php

namespace App;

use App\Events\GenerateUUID;

use App\ModelTraits\AutomateMutators;
use App\ModelTraits\BinHex;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use AutomateMutators;
    use BinHex;

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => GenerateUUID::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'uuid', 'campaign_id',
      'creative_contents', 'creative_type', 'creative_sha1', 'creative_width', 'creative_height',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
    * The attributes that use some ModelTraits with mutator settings automation
    *
    * @var array
    */
    protected $traitAutomate = [
      'uuid' => 'BinHex',
      'creative_sha1' => 'BinHex',
    ];
}
