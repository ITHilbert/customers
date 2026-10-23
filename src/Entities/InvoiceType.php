<?php

namespace ITHilbert\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use ITHilbert\LaravelKit\Traits\VueComboBox;

class InvoiceType extends Model
{
    protected $table = 'invoice_types';

    protected $fillable = ['invoice_type'];

    public $timestamps = false;

    use VueComboBox;

    public function getCbCaptionAttribute()
    {
        return $this->invoice_type;
    }
}
