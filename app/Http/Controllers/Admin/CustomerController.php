<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends AdminController
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->withCount('leads')
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn (Builder $query) => $query->where('name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('company', 'like', $term)
                    ->orWhere('city', 'like', $term));
            })
            ->when($request->filled('type'), fn (Builder $query) => $query->where('type', $request->input('type')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'filters' => $request->only(['q', 'type']),
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.form', ['customer' => new Customer(['type' => 'retail'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = Customer::query()->create($this->validated($request));

        return redirect()->route('admin.customers.show', $customer)->with('status', 'Contact added.');
    }

    public function show(Customer $customer): View
    {
        $customer->load('leads.product');

        return view('admin.customers.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.form', ['customer' => $customer]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->validated($request, $customer));

        return redirect()->route('admin.customers.show', $customer)->with('status', 'Contact updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('status', 'Contact deleted. Their enquiries were kept.');
    }

    private function validated(Request $request, ?Customer $customer = null): array
    {
        $request->merge(['phone' => Customer::normalisePhone($request->input('phone'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('customers', 'phone')->ignore($customer)],
            'email' => ['nullable', 'email', 'max:160', Rule::unique('customers', 'email')->ignore($customer)],
            'company' => ['nullable', 'string', 'max:160'],
            'type' => ['required', Rule::in(array_keys(Customer::TYPES))],
            'city' => ['nullable', 'string', 'max:80'],
            'state' => ['nullable', 'string', 'max:80'],
            'gst_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9A-Z]{15}$/i'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'phone.unique' => 'Another contact already uses this phone number.',
            'email.unique' => 'Another contact already uses this email.',
            'gst_number.regex' => 'A GSTIN is 15 letters and numbers.',
        ]);
    }
}
