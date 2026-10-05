@extends('admin.layouts.app')

@section('title', $redirect->exists ? 'Edit redirect' : 'Add redirect')

@section('actions')
    <a class="btn" href="{{ route('admin.redirects.index') }}">← All redirects</a>
@endsection

@section('content')
    <form class="card" method="post" action="{{ $redirect->exists ? route('admin.redirects.update', $redirect) : route('admin.redirects.store') }}" style="max-width:760px">
        @csrf
        @if ($redirect->exists) @method('PUT') @endif
        @include('admin.partials.field', ['name' => 'from_path', 'label' => 'Old URL path', 'value' => $redirect->from_path, 'required' => true, 'placeholder' => '/old-page',
            'hint' => 'The path after your domain, e.g. /products/old-product-name. Query strings are ignored.'])
        @include('admin.partials.field', ['name' => 'to_url', 'label' => 'Send visitors to', 'value' => $redirect->to_url, 'required' => true, 'placeholder' => '/products/new-product-name',
            'hint' => 'A path on this site (starting with /) or a full https:// address.'])
        @include('admin.partials.select', ['name' => 'status_code', 'label' => 'Type', 'options' => [301 => '301 — Permanent (passes SEO value, use this normally)', 302 => '302 — Temporary'], 'value' => $redirect->status_code])
        @include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Active', 'checked' => $redirect->is_active])
        <div class="form-actions"><button class="btn btn-primary" type="submit">Save redirect</button></div>
    </form>
@endsection
