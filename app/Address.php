<?php

namespace Walladog;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'partner_id', 'site_id', 'address1', 'address2', 'province_txt', 'city_txt', 'cp_txt'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];


    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function partner(){
        return $this->belongsTo(Partner::class);
    }

    public function site(){
        return $this->belongsTo(Site::class,'site_id');
    }
}
