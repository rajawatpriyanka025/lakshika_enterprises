@extends('admin.layouts.app')

@section('title', $marketplace->exists ? 'Edit '.$marketplace->name : 'Add marketplace')

@section('actions')
    <a class="btn" href="{{ route('admin.marketplaces.index') }}">← All marketplaces</a>
@endsection

@section('content')
    <form class="card" method="post" action="{{ $marketplace->exists ? route('admin.marketplaces.update', $marketplace) : route('admin.marketplaces.store') }}" style="max-width:760px">
        @csrf
        @if ($marketplace->exists) @method('PUT') @endif
        @include('admin.partials.field', ['name' => 'name', 'label' => 'Name', 'value' => $marketplace->name, 'required' => true, 'placeholder' => 'Amazon'])
        @include('admin.partials.field', ['name' => 'store_url', 'label' => 'Your brand store / seller page', 'type' => 'url', 'value' => $marketplace->store_url,
            'hint' => 'Optional. Used for footer and contact page links. Without it, a brand search is used.'])
        <div class="grid-2">
            @include('admin.partials.field', ['name' => 'search_url', 'label' => 'Search URL', 'type' => 'url', 'value' => $marketplace->search_url, 'required' => true, 'placeholder' => 'https://www.amazon.in/s'])
            @include('admin.partials.field', ['name' => 'search_parameter', 'label' => 'Search parameter', 'value' => $marketplace->search_parameter, 'required' => true, 'hint' => 'Amazon uses k, Flipkart and Meesho use q.'])
            @include('admin.partials.field', ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'value' => $marketplace->sort_order, 'attrs' => 'min="0"'])
            <div style="padding-top:30px">@include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Show on website', 'checked' => $marketplace->is_active])</div>
        </div>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Save marketplace</button></div>
    </form>
@endsection
