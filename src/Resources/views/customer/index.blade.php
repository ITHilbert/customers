@extends('layouts.customer')

@section('title', Lang::get('customer::customer.header_list'))

{{-- @section('content_header')
@stop --}}

@section('content')
<card title="@lang('customer::customer.header_list')">

  @include('include.message')

  <button-create route="{{ route('customer.create') }}">Neuer Kunde</button-create>

  <table class="table table-bordered data-table">
      <thead>
      <tr>
          <th width="50px">Nr.</th>
          <th>@lang('customer::customer.name')</th>
          <th>@lang('customer::customer.phone')</th>
          <th>@lang('customer::customer.mobile')</th>
          <th>@lang('customer::customer.email')</th>
          <th width="100px"></th>
      </tr>
      </thead>
      <tbody>
      </tbody>
  </table>


</card>


<dialog-delete title="Kunden löschen" body="Wollen Sie wirklich diesen Kunden löschen?" route="{{ route('customer.delete',0) }}" ></dialog-delete>
@stop


@section('js')
<script>
  $(function() {

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        language: { url: "{{ asset("DataTable_DE.json ") }}" },
        ajax: "{{ route('customer.list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'cname', name: 'cname' },
            { data: 'phone', name: 'phone' },
            { data: 'mobile', name: 'mobile' },
            { data: 'email', name: 'email' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });

  });
</script>
@stop
