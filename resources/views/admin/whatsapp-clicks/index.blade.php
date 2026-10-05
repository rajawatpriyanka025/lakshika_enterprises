@extends('admin.layouts.app')

@section('title', 'WhatsApp enquiries')
@section('subtitle', 'Every time a visitor taps a WhatsApp button on the website, with the product and topic they chose. When a chat turns into a real enquiry, log it as a lead to track it.')

@section('actions')
    <a class="btn" href="{{ route('admin.settings.edit', 'crm') }}">WhatsApp settings</a>
    <a class="btn btn-primary" href="{{ route('admin.leads.create', ['source' => 'whatsapp']) }}">+ Log a WhatsApp lead</a>
@endsection

@section('content')
    @unless (setting('whatsapp_number'))
        <div class="flash flash-error">WhatsApp buttons are hidden because no number is set. <a href="{{ route('admin.settings.edit', 'crm') }}">Add your WhatsApp number</a>.</div>
    @endunless

    <div class="stat-grid">
        <div class="stat"><span>Chats started · 30 days</span><strong>{{ $total30 }}</strong></div>
        <div class="stat"><span>Unique visitors · 30 days</span><strong>{{ $unique30 }}</strong></div>
        @foreach ($byTopic as $topicRow)
            <div class="stat"><span>{{ $topicRow->topicLabel() }} · 30 days</span><strong>{{ $topicRow->total }}</strong></div>
        @endforeach
    </div>

    <div class="grid-2">
        <div class="card">
            <h2>By product · 30 days</h2>
            @forelse ($byProduct as $row)
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--line)">
                    <span>{{ $row->product?->name ?? 'General (no product)' }}</span><strong>{{ $row->total }}</strong>
                </div>
            @empty
                <p class="muted">No clicks yet.</p>
            @endforelse
        </div>
        <div class="card">
            <h2>Recent clicks</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>When</th><th>Enquiry</th><th><span class="sr-only">Actions</span></th></tr></thead>
                    <tbody>
                        @forelse ($clicks as $click)
                            <tr>
                                <td>{{ $click->created_at->format('j M, g:i a') }}<span class="sub">{{ $click->page_url ? \Illuminate\Support\Str::limit(parse_url($click->page_url, PHP_URL_PATH) ?: '/', 32) : '' }}{{ $click->utm_source ? ' · '.$click->utm_source : '' }}</span></td>
                                <td>{{ $click->topicLabel() }}<span class="sub">{{ $click->product?->name }}</span></td>
                                <td class="table-actions"><a class="btn btn-sm" href="{{ route('admin.leads.create', array_filter(['source' => 'whatsapp', 'type' => $click->topic, 'product' => $click->product_id])) }}">Log as lead</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty">No clicks yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('admin.partials.pagination', ['paginator' => $clicks])
        </div>
    </div>
@endsection
