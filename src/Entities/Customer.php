<?php

namespace ITHilbert\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use ITHilbert\Customer\Entities\CustomerType;
use ITHilbert\Customer\Entities\InvoiceType;
use ITHilbert\Customer\Entities\NameAddress;

use ITHilbert\Vue\Entities\ComboBox;
use ITHilbert\Vue\Traits\VueComboBox;

class Customer extends Model
{
	use SoftDeletes;
	use VueComboBox;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['customer_type_id', 'company_row_1', 'company_row_2', 'address_id', 'titel', 'first_name', 'last_name', 'street', 'postcode', 'city', 'country', 'ustid'
							,'phone', 'mobile', 'fax', 'email', 'web', 'invoice_type_id', 'hourly_rate', 'mileage_allowance', 'comment', 'edit_user_id'	];


	/* ComboBox */
	public function getCbCaptionAttribute(){
		return $this->getName();
	}

	public static function getComboBoxData(){
		$options = Customer::all();

        $liste = array();
        $anz = 0;
        foreach($options as $option){
            $liste[$anz] = new ComboBox();
            $liste[$anz]->cbKey = $option->cbKey;
            $liste[$anz]->cbCaption = $option->getName();
            $anz++;
        }

        return json_encode($liste);
    }

	/**
	 * Get the user's full name.
	 *
	 * @return string
	 */
	public function getFullNameAttribute()
	{
		return "{$this->first_name} {$this->last_name}";
	}

	/**
	 * Get the user's full name.
	 *
	 * @return string
	 */
	public function getNameAttribute()
	{
		return "{$this->first_name} {$this->last_name}";
	}

	public function getNameAddressAttribute()
	{
		if($this->address_id==1){
			return "Herr";
		}else if($this->address_id==2){
			return "Frau";
		}else{
			return $this->address_id;
		}
	}

	public function getNameAddressText()
	{
		$adr = NameAddress::find($this->address_id);
		return $adr->address;
	}

	public function getCustomerTypeText()
	{
		$ct = CustomerType::find($this->address_id);
		return $ct->customer_type;
	}

	public function getInvoiceTypeText()
	{
		$it = InvoiceType::find($this->address_id);
		return $it->invoice_type;
	}

	public function invoiceType()
	{
		return $this->belongsTo('ITHilbert\Customer\Entities\InvoiceType','invoice_typ_id','id')->withDefault();
	}



	public function getName(){
		if($this->customer_type_id == 1){
			return $this->company_row_1;
		}else{
			return $this->last_name . ', ' . $this->first_name;
		}
	}



	public function getCustomerTypesCB(){
		return CustomerType::getComboBoxData();
	}

	public function getInvoiceTypesCB(){
		return InvoiceType::getComboBoxData();
	}

	public function getNameAddressCB(){
		return NameAddress::getComboBoxData();
	}


}
