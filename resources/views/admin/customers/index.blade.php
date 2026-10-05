@extends('admin.layouts.app')

@section('title', 'Customers & dealers')
@section('subtitle', 'Everyone who has enquired, plus wholesale buyers and dealers you add yourself.')

@section('actions')
    <a class="btn btn-primary" href="{{ route('admin.customers.create') }}">+ Add contact</a>
@endsection

@section('content')
    <form class="filters" method="get" role="search">
        <div class="field"><label class="sr-only" for="q">Search</label><input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, phone, company, city…"></div>
        <div class="field">
            <label class="sr-only" for="type">Type</label>
            <select id="type" name="type">
                <option value="">All types</option>
                @foreach (\App\Models\Customer::TYPES as $typeKey => $typeLabel)
                    <option value="{{ $typeKey }}" @selected(($filters['type'] ?? '') === $typeKey)>{{ $typeLabel }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn" type="submit">Filter</button>
        @if (array_filter($filters))<a class="btn" href="{{ route('admin.customers.index') }}">Clear</a>@endif
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Type</th><th>Phone</th><th>City</th><th class="num">Enquiries</th><th>Added</th></tr></thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td><a class="row-link" href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a>@if ($customer->company)<span class="sub">{{ $customer->company }}</span>@endif</td>
                        <td><span class="badge">{{ $customer->typeLabel() }}</span></td>
                        <td>{{ $customer->phone ?: '—' }}@if ($customer->email)<span class="sub">{{ $customer->email }}</span>@endif</td>
                        <td>{{ $customer->city ?: '—' }}</td>
                        <td class="num">{{ $customer->leads_count }}</td>
                        <td>{{ $customer->created_at->format('j M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No contacts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('admin.partials.pagination', ['paginator' => $customers])
@endsection
