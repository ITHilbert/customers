@extends('layouts.customer')

@section('title', Lang::get('customer::customer.header_create'))

{{-- @section('content_header')
@stop --}}


@section('content')
<card title="@lang('customer::customer.header_create')">
<div>
    @include('include.message')
    <hform action="{{ route('customer.store') }}">
        {{-- Kundentyp --}}
        <div class="form-group row mb-2">
            <label for="customer_type_id" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.customer_type_id')</label>
            <div class="col-md-6">
            <combobox name="customer_type_id" :options="{{ $customer->getCustomerTypesCB() }}" value="{{ old('customer_type_id', 1) }}"></combobox>
            </div>
        </div>


        <div class="form-group row mb-2" id="divFirma1">
            <label for="company_row_1" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.company_row_1')</label>
            <div class="col-md-6">
               <input-text name="company_row_1" value="{{ old('company_row_1', $customer->company_row_1) }}" />
            </div>
        </div>


        <div class="form-group row mb-2" id="divFirma2">
            <label for="company_row_2" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.company_row_2')</label>
            <div class="col-md-6">
               <input-text name="company_row_2" value="{{ old('company_row_2', $customer->company_row_2) }}" />
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="address_id" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.address_id')</label>
            <div class="col-md-6">
                <combobox name="address_id" value="{{ old('address_id', 1) }}" :options="{{ $customer->getNameAddressCB() }}"></combobox>
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="titel" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.titel')</label>
            <div class="col-md-6">
               <input-text name="titel" value="{{ old('titel', $customer->titel) }}" />
            </div>
        </div>


        <div class="form-group row mb-2">
            <label for="first_name" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.first_name')</label>
            <div class="col-md-6">
               <input-text name="first_name" value="{{ old('first_name', $customer->first_name) }}" />
            </div>
        </div>


        <div class="form-group row mb-2">
            <label for="last_name" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.last_name')</label>
            <div class="col-md-6">
               <input-text name="last_name" value="{{ old('last_name', $customer->last_name) }}" />
            </div>
        </div>


        <div class="form-group row mb-2">
            <label for="street" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.street')</label>
            <div class="col-md-6">
               <input-text name="street" value="{{ old('street', $customer->street) }}" />
            </div>
        </div>


        <div class="form-group row mb-2">
            <label for="postcode" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.postcode')</label>
            <div class="col-md-6">
               <input-text name="postcode" value="{{ old('postcode', $customer->postcode) }}" />
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="city" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.city')</label>
            <div class="col-md-6">
               <input-text name="city" value="{{ old('city', $customer->city) }}" />
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="country" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.country')</label>
            <div class="col-md-6">
               <input-text name="country" value="{{ old('country', $customer->country) }}" />
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="invoice_type_id" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.invoice_type')</label>
            <div class="col-md-6">
                <combobox name="invoice_type_id" value="{{ old('invoice_type_id', 1) }}" :options="{{ $customer->getInvoiceTypesCB() }}"></combobox>
            </div>
        </div>

        <div class="form-group row mb-2" id="divUStID">
            <label for="ustid" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.ustid')</label>
            <div class="col-md-6">
               <input-text name="ustid" value="{{ old('ustid', $customer->ustid) }}" />
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="phone" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.phone')</label>
            <div class="col-md-6">
               <input-text name="phone" value="{{ old('phone', $customer->phone) }}" />
            </div>
        </div>


        <div class="form-group row mb-2">
            <label for="mobile" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.mobile')</label>
            <div class="col-md-6">
               <input-text name="mobile" value="{{ old('mobile', $customer->mobile) }}" />
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="fax" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.fax')</label>
            <div class="col-md-6">
                <input-text name="fax" value="{{ old('fax', $customer->fax) }}" />
            </div>
        </div>


        <div class="form-group row mb-2">
            <label for="email" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.email')</label>
            <div class="col-md-6">
               <input-text name="email" value="{{ old('email', $customer->email) }}" />
            </div>
        </div>



        <div class="form-group row mb-2">
            <label for="web" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.web')</label>
            <div class="col-md-6">
               <input-text name="web" value="{{ old('web', $customer->web) }}" />
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="hourly_rate" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.hourly_rate')</label>
            <div class="col-md-6">
               <input-euro name="hourly_rate" value="{{ old('hourly_rate', $customer->hourly_rate) }}" ></input-euro>
            </div>
        </div>


        <div class="form-group row mb-2">
            <label for="mileage_allowance" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.mileage_allowance')</label>
            <div class="col-md-6">
                <input-euro name="mileage_allowance" value="{{ old('mileage_allowance', $customer->mileage_allowance) }}"></input-euro>
            </div>
        </div>

        <div class="form-group row mb-2">
            <label for="comment" class="col-md-4 col-form-label text-md-right">@lang('customer::customer.comment')</label>
            <div class="col-md-6">
               <text-area name="comment">{{ old('comment', $customer->comment) }}</textarea>
            </div>
        </div>


        <div class="form-group row mb-2">
            <div class="col-md-4 text-right">
                <button-back route="{{ route('customer.list') }}">@Lang('master.btn-back')</button-back>
            </div>
            <div class="col-md-6 text-left">
                <button-save>@lang('master.btn-save')</button-save>
            </div>
        </div>
    </hform>
</div>
</card>
@stop
