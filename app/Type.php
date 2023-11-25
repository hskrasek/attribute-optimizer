<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperType
 */
class Type extends Model
{
    protected $table = 'invTypes';

    public function group()
    {
        return $this->belongsTo(Group::class, 'groupID', 'groupID');
    }
}
