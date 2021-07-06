<?php

namespace ITHilbert\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use ITHilbert\Vue\Traits\VueComboBox;

class CustomerType extends Model
{
    protected $table ='customer_types';
    public $timestamps = false;

    use VueComboBox;

    public function getCbCaptionAttribute(){
        return $this->customer_type;
    }
}
