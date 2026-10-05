@extends('admin.layouts.app')

@section('title', 'Add lead')
@section('subtitle', 'Record an enquiry that came in by phone, WhatsApp, a marketplace or a referral.')

@section('actions')
    <a class="btn" href="{{ route('admin.leads.index') }}">← All leads</a>
@endsection

@section('content')
    <form method="post" action="{{ route('admin.leads.store') }}">
        @csrf
        @if ($customer)<input type="hidden" name="customer_id" value="{{ $customer->id }}">@endif
        <div class="form-layout">
            <div class="card">
                <h2>Contact</h2>
                <div class="grid-2">
                    @include('admin.partials.field', ['name' => 'name', 'label' => 'Name', 'value' => $customer?->name, 'required' => true])
                    @include('admin.partials.field', ['name' => 'phone', 'label' => 'Phone / WhatsApp', 'type' => 'tel', 'value' => $customer?->phone])
                    @include('admin.partials.field', ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'value' => $customer?->email])
                    @include('admin.partials.field', ['name' => 'city', 'label' => 'City', 'value' => $customer?->city])
                </div>
                @if ($customer)
                    <p class="muted">Linked to existing contact <a href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a>.</p>
                @else
                    <p class="muted">If the phone or email matches an existing contact, the lead is linked to them automatically.</p>
                @endif
                <h2 style="margin-top:20px">Enquiry</h2>
                <div class="grid-2">
                    @include('admin.partials.select', ['name' => 'enquiry_type', 'label' => 'Enquiry type', 'options' => \App\Models\Lead::ENQUIRY_TYPES, 'value' => $lead->enquiry_type])
                    @include('admin.partials.select', ['name' => 'product_id', 'label' => 'Product', 'options' => $products, 'value' => $lead->product_id, 'placeholder' => '— None —'])
                    @include('admin.partials.field', ['name' => 'quantity', 'label' => 'Quantity', 'type' => 'number', 'attrs' => 'min="1"'])
                </div>
                @include('admin.partials.textarea', ['name' => 'message', 'label' => 'Details', 'rows' => 5])
            </div>
            <aside>
                <div class="card">
                    <h2>Tracking</h2>
                    @include('admin.partials.select', ['name' => 'source', 'label' => 'Source', 'options' => \App\Models\Lead::SOURCES, 'value' => $lead->source])
                    @include('admin.partials.select', ['name' => 'status', 'label' => 'Status', 'options' => \App\Models\Lead::STATUSES, 'value' => $lead->status])
                    @include('admin.partials.field', ['name' => 'follow_up_at', 'label' => 'Follow up on', 'type' => 'date'])
                    <button class="btn btn-primary btn-block" type="submit">Save lead</button>
                </div>
            </aside>
        </div>
    </form>
@endsection
