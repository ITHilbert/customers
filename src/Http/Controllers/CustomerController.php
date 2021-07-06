<?php

namespace ITHilbert\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;

use Yajra\DataTables\Facades\DataTables;

use ITHilbert\Customer\Entities\Customer;
use ITHilbert\LaravelKit\Helpers\HButton;



class CustomerController extends Controller
{
    //***************************************
    //
    //     Read all customer
    //
    //****************************************
    public function index(Request $request)
    {
        $data = Customer::latest()->get();

        if ($request->ajax()) {
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('cname', function($row){
                            return $row->getName();
                    })
                    ->addColumn('action', function($row){
                        $ausgabe = '<div style="white-space: nowrap;">';
                        $ausgabe .= HButton::show(route('customer.show', $row->id), '');
                        $ausgabe .= HButton::edit(route('customer.edit', $row->id), '');
                        $ausgabe .= HButton::project(route('projects.index', $row->id), '');
                        $ausgabe .= HButton::delete($row->id, '');
                        $ausgabe .= '</div>';

                        return $ausgabe;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('customer::customer.index');
    }

    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new Customer
    //
    //****************************************
    public function create()
    {
        $customer = new Customer();

        $customer->hourly_rate = Config::get('customer.hourly_rate');
        $customer->mileage_allowance = Config::get('customer.mileage_allowance');

        return view('customer::customer.create')->with(compact('customer'));
    }

    /**
     * POST   - Store Customer     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if($request->customer_type_id == 1){
            $request->validate([
                'customer_type_id' => 'required',
                'company_row_1' => 'required',
                'address_id' => 'required',
                'email' => 'email|required',
                'invoice_type_id' => 'required',
                'hourly_rate' => 'required|numeric',
                'mileage_allowance' => 'required|numeric',
            ]);
        }else{
            $request->validate([
                'customer_type_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'address_id' => 'required',
                'email' => 'email|required',
                'invoice_type_id' => 'required',
                'hourly_rate' => 'required|numeric',
                'mileage_allowance' => 'required|numeric',
            ]);
        }

        $customer = Customer::create($request->all());

        if($customer){
            $customer->edit_user_id = Auth::Id();
            $customer->update();

            return redirect()->route('customer.list')->with([
                'message'    => Lang::get('customer::customer.MsgAddSuccess'),
                'alert-type' => 'success',
            ]);
        }else{
            return redirect()->back();
        }

    }

    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an Customer
    //
    //****************************************
    public function edit($id)
    {
            $customer = Customer::findOrFail($id);
            return view('customer::customer.edit')->with(compact('customer'));
    }
    //***************************************
    //
    //      Update customer
    //
    //****************************************

    public function update(Request $request, $id)
    {
        if($request->customer_type_id == 1){
            $request->validate([
                'customer_type_id' => 'required',
                'company_row_1' => 'required',
                'address_id' => 'required',
                'email' => 'email|required',
                'invoice_type_id' => 'required',
                'hourly_rate' => 'required|numeric',
                'mileage_allowance' => 'required|numeric',
            ]);
        }else{
            $request->validate([
                'customer_type_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'address_id' => 'required',
                'email' => 'email|required',
                'invoice_type_id' => 'required',
                'hourly_rate' => 'required|numeric',
                'mileage_allowance' => 'required|numeric',
            ]);
        }


        //die($request->customer_type_id);
        $input = $request->all();
        $input['edit_user_id'] = Auth::Id();
        $customer = Customer::findOrFail($id);
        $update= $customer->update($input);

        if($update){
            return redirect()->route('customer.list')->with([
                'message'    => Lang::get('customer::customer.MsgEditSuccess'),
                'alert-type' => 'success',
            ]);

        }else{
            return redirect()->back();
        }

    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item
    //
    //****************************************
    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customer.list')->with([
            'message'    =>"Customer deleted successfully.",
            'alert-type' => 'success',
        ]);
    }
    //***************************************
    //
    //
    //  View an Customer
    //
    //****************************************
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer::customer.show')->with(compact('customer'));
    }


}
