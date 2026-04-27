<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return response(
                Customer::all()
            );
        }
        $customers = Customer::latest()->paginate(10);
        return view('customers.index')->with('customers', $customers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerStoreRequest $request)
    {
        $avatar_path = '';

        if ($request->hasFile('avatar')) {
            $avatar_path = $request->file('avatar')->store('customers', 'public');
        }

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'document_id' => $request->document_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'avatar' => $avatar_path,
            'user_id' => $request->user()->id,
            'notes' => $request->notes,
            'credit_limit' => $request->credit_limit,
            'birthday' => $request->birthday,
            'tags' => $request->tags,
        ]);

        if (!$customer) {
            return redirect()->back()->with('error', 'Lo sentimos, algo salió mal al crear el cliente.');
        }
        return redirect()->route('customers.index')->with('success', '¡Éxito, el nuevo cliente ha sido añadido!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $orders = $customer->orders()->latest()->paginate(10);
        return view('customers.show', compact('customer', 'orders'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->document_id = $request->document_id;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->notes = $request->notes;
        $customer->credit_limit = $request->credit_limit;
        $customer->birthday = $request->birthday;
        $customer->tags = $request->tags;


        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($customer->avatar) {
                Storage::delete($customer->avatar);
            }
            // Store avatar
            $avatar_path = $request->file('avatar')->store('customers', 'public');
            // Save to Database
            $customer->avatar = $avatar_path;
        }

        if (!$customer->save()) {
            return redirect()->back()->with('error', 'Lo sentimos, algo salió mal al actualizar el cliente.');
        }
        return redirect()->route('customers.index')->with('success', 'Éxito, el cliente ha sido actualizado.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->avatar) {
            Storage::delete($customer->avatar);
        }

        $customer->delete();

       return response()->json([
           'success' => true
       ]);
    }

    public function report(Customer $customer)
    {
        $orders = $customer->orders()->latest()->get();
        $pdf = Pdf::loadView('customers.report', compact('customer', 'orders'));
        return $pdf->download('reporte-cliente-' . $customer->id . '.pdf');
    }
}
