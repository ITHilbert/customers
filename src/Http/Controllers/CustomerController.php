<?php

namespace ITHilbert\Customer\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use ITHilbert\Customer\Entities\Customer;
use ITHilbert\Customer\Http\Requests\KundeSpeichernRequest;
use ITHilbert\LaravelKit\Helpers\HButton;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    // ***************************************
    //
    //     Read all customer
    //
    // ****************************************
    public function index(Request $request)
    {
        $data = Customer::latest()->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('cname', function ($row) {
                    return $row->getName();
                })
                ->addColumn('action', function ($row) {
                    $ausgabe = '<div style="white-space: nowrap;">';
                    $ausgabe .= HButton::show(route('customer.show', $row->id), '');
                    $ausgabe .= HButton::edit(route('customer.edit', $row->id), '');
                    $ausgabe .= HButton::delete($row->id, '');
                    $ausgabe .= '</div>';

                    return $ausgabe;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('customer::customer.index');
    }

    // ***************************************
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
    // ****************************************
    public function create()
    {
        $customer = new Customer;

        $customer->hourly_rate = Config::get('customer.hourly_rate');
        $customer->mileage_allowance = Config::get('customer.mileage_allowance');

        return view('customer::customer.create')->with(compact('customer'));
    }

    /**
     * POST - Kunde anlegen; der bearbeitende Benutzer wird serverseitig gesetzt.
     */
    public function store(KundeSpeichernRequest $request): RedirectResponse
    {
        $customer = new Customer($request->validated());
        $customer->edit_user_id = $request->user()->getAuthIdentifier();
        $customer->save();

        return redirect()->route('customer.list')->with([
            'message' => Lang::get('customer::customer.MsgAddSuccess'),
            'alert-type' => 'success',
        ]);
    }

    // ***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an Customer
    //
    // ****************************************
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        return view('customer::customer.edit')->with(compact('customer'));
    }
    // ***************************************
    //
    //      Update customer
    //
    // ****************************************

    public function update(KundeSpeichernRequest $request, $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->fill($request->validated());
        $customer->edit_user_id = $request->user()->getAuthIdentifier();
        $customer->save();

        return redirect()->route('customer.list')->with([
            'message' => Lang::get('customer::customer.MsgEditSuccess'),
            'alert-type' => 'success',
        ]);
    }

    // ***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item
    //
    // ****************************************
    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customer.list')->with([
            'message' => 'Customer deleted successfully.',
            'alert-type' => 'success',
        ]);
    }

    // ***************************************
    //
    //
    //  View an Customer
    //
    // ****************************************
    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        return view('customer::customer.show')->with(compact('customer'));
    }
}
