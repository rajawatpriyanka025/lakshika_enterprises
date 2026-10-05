@extends('admin.layouts.app')

@section('title', $customer->exists ? 'Edit '.$customer->name : 'Add contact')

@section('actions')
    <a class="btn" href="{{ $customer->exists ? route('admin.customers.show', $customer) : route('admin.customers.index') }}">← Back</a>
@endsection

@section('content')
    <form method="post" action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}">
        @csrf
        @if ($customer->exists) @method('PUT') @endif
        <div class="form-layout">
            <div class="card">
                <div class="grid-2">
                    @include('admin.partials.field', ['name' => 'name', 'label' => 'Name', 'value' => $customer->name, 'required' => true])
                    @include('admin.partials.field', ['name' => 'company', 'label' => 'Company / shop', 'value' => $customer->company])
                    @include('admin.partials.field', ['name' => 'phone', 'label' => 'Phone / WhatsApp', 'type' => 'tel', 'value' => $customer->phone])
                    @include('admin.partials.field', ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'value' => $customer->email])
                    @include('admin.partials.field', ['name' => 'city', 'label' => 'City', 'value' => $customer->city])
                    @include('admin.partials.field', ['name' => 'state', 'label' => 'State', 'value' => $customer->state])
                </div>
                @include('admin.partials.textarea', ['name' => 'address', 'label' => 'Address', 'value' => $customer->address, 'rows' => 3])
                @include('admin.partials.textarea', ['name' => 'notes', 'label' => 'Notes', 'value' => $customer->notes, 'rows' => 4, 'hint' => 'Preferences, pricing agreed, payment terms…'])
            </div>
            <aside>
                <div class="card">
                    @include('admin.partials.select', ['name' => 'type', 'label' => 'Type', 'options' => \App\Models\Customer::TYPES, 'value' => $customer->type])
                    @include('admin.partials.field', ['name' => 'gst_number', 'label' => 'GSTIN', 'value' => $customer->gst_number, 'hint' => 'For wholesale and dealer invoices.'])
                    <button class="btn btn-primary btn-block" type="submit">Save contact</button>
                </div>
            </aside>
        </div>
    </form>
@endsection
