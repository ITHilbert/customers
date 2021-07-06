<?php

namespace ITHilbert\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use ITHilbert\Vue\Traits\VueComboBox;

class NameAddress extends Model
{
    protected $table ='name_address';
    protected $fillable = ['address'];
    public $timestamps = false;

    use VueComboBox;

    public function getCbCaptionAttribute(){
        return $this->address;
    }
}
