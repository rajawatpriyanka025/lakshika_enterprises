@extends('admin.layouts.app')

@section('title', 'Leads')
@section('subtitle', 'Track WhatsApp, phone and marketplace enquiries from first message to order.')

@section('actions')
    <a class="btn" href="{{ route('admin.leads.export', request()->query()) }}">Export CSV</a>
    <a class="btn btn-primary" href="{{ route('admin.leads.create') }}">+ Add lead</a>
@endsection

@section('content')
    <div class="chips">
        <a class="chip {{ empty($filters['status']) && empty($filters['follow_up']) ? 'is-active' : '' }}" href="{{ route('admin.leads.index') }}">All · {{ $counts->sum() }}</a>
        @foreach (\App\Models\Lead::STATUSES as $statusKey => $statusLabel)
            <a class="chip {{ ($filters['status'] ?? '') === $statusKey ? 'is-active' : '' }}" href="{{ route('admin.leads.index', ['status' => $statusKey]) }}">{{ $statusLabel }} · {{ $counts[$statusKey] ?? 0 }}</a>
        @endforeach
        <a class="chip {{ ! empty($filters['follow_up']) ? 'is-active' : '' }}" href="{{ route('admin.leads.index', ['follow_up' => 1]) }}">Follow-up due</a>
    </div>

    <form class="filters" method="get" role="search">
        @if (! empty($filters['status']))<input type="hidden" name="status" value="{{ $filters['status'] }}">@endif
        <div class="field"><label class="sr-only" for="q">Search</label><input id="q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, phone, email, city…"></div>
        <div class="field">
            <label class="sr-only" for="source">Source</label>
            <select id="source" name="source">
                <option value="">All sources</option>
                @foreach (\App\Models\Lead::SOURCES as $sourceKey => $sourceLabel)
                    <option value="{{ $sourceKey }}" @selected(($filters['source'] ?? '') === $sourceKey)>{{ $sourceLabel }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="sr-only" for="type">Enquiry type</label>
            <select id="type" name="type">
                <option value="">All enquiry types</option>
                @foreach (\App\Models\Lead::ENQUIRY_TYPES as $typeKey => $typeLabel)
                    <option value="{{ $typeKey }}" @selected(($filters['type'] ?? '') === $typeKey)>{{ $typeLabel }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn" type="submit">Filter</button>
        @if (array_filter($filters))<a class="btn" href="{{ route('admin.leads.index') }}">Clear</a>@endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Lead</th><th>Enquiry</th><th>Source</th><th>Status</th><th>Follow up</th><th>Received</th><th><span class="sr-only">Actions</span></th></tr>
            </thead>
            <tbody>
                @forelse ($leads as $lead)
                    <tr>
                        <td>
                            <a class="row-link" href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a>
                            <span class="sub">{{ $lead->phone }}{{ $lead->city ? ' · '.$lead->city : '' }}</span>
                        </td>
                        <td>{{ $lead->enquiryTypeLabel() }}<span class="sub">{{ $lead->product?->name }}{{ $lead->quantity ? ' · Qty '.$lead->quantity : '' }}</span></td>
                        <td>{{ $lead->sourceLabel() }}@if ($lead->utm_source)<span class="sub">{{ $lead->utm_source }}</span>@endif</td>
                        <td><span class="badge badge-{{ $lead->status }}">{{ $lead->statusLabel() }}</span></td>
                        <td>
                            @if ($lead->follow_up_at)
                                <span class="{{ $lead->follow_up_at->endOfDay()->isPast() && ! in_array($lead->status, ['won', 'lost']) ? 'badge badge-danger' : '' }}">{{ $lead->follow_up_at->format('j M') }}</span>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td>{{ $lead->created_at->format('j M Y') }}<span class="sub">{{ $lead->created_at->format('g:i a') }}</span></td>
                        <td class="table-actions">
                            @if ($lead->whatsappUrl())<a class="btn btn-sm btn-whatsapp" href="{{ $lead->whatsappUrl() }}" target="_blank" rel="noopener">WhatsApp</a>@endif
                            <a class="btn btn-sm" href="{{ route('admin.leads.show', $lead) }}">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">No leads match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('admin.partials.pagination', ['paginator' => $leads])
@endsection
