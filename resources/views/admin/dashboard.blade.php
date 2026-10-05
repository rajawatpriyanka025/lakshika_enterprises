@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Good '.(now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')).', '.auth()->user()->name.'. Here is what is happening.')

@section('actions')
    <a class="btn" href="{{ route('admin.leads.create') }}">+ Add lead</a>
    <a class="btn btn-primary" href="{{ route('admin.products.create') }}">+ Add product</a>
@endsection

@section('content')
    <div class="stat-grid">
        <a class="stat" href="{{ route('admin.leads.index', ['status' => 'new']) }}"><span>New leads</span><strong>{{ $stats['new_leads'] }}</strong><small>waiting for a reply</small></a>
        <a class="stat" href="{{ route('admin.leads.index') }}"><span>Leads · 30 days</span><strong>{{ $stats['leads_30'] }}</strong><small>{{ $stats['won_30'] }} won</small></a>
        <a class="stat" href="{{ route('admin.whatsapp-clicks.index') }}"><span>WhatsApp clicks · 30 days</span><strong>{{ $stats['whatsapp_30'] }}</strong><small>chats started from the site</small></a>
        <a class="stat" href="{{ route('admin.customers.index') }}"><span>Contacts</span><strong>{{ $stats['customers'] }}</strong><small>buyers, dealers &amp; wholesale</small></a>
        <a class="stat" href="{{ route('admin.products.index') }}"><span>Live products</span><strong>{{ $stats['products'] }}</strong><small>on the website</small></a>
    </div>

    @php
        $pipelineTotal = max(1, $pipeline->sum());
        $pipelineColours = ['new' => '#3d7cc0', 'contacted' => '#7aa7d6', 'qualified' => '#8b6fd1', 'quoted' => '#d99a2b', 'won' => '#1d7a46', 'lost' => '#b8c2cc'];
    @endphp
    @if ($pipeline->sum() > 0)
        <div class="card">
            <h2>Lead pipeline</h2>
            <div class="pipeline" role="img" aria-label="Lead pipeline by status">
                @foreach (\App\Models\Lead::STATUSES as $statusKey => $statusLabel)
                    @if ($pipeline[$statusKey] ?? 0)
                        <span style="width: {{ ($pipeline[$statusKey] / $pipelineTotal) * 100 }}%; background: {{ $pipelineColours[$statusKey] }}" title="{{ $statusLabel }}: {{ $pipeline[$statusKey] }}"></span>
                    @endif
                @endforeach
            </div>
            <div class="pipeline-legend">
                @foreach (\App\Models\Lead::STATUSES as $statusKey => $statusLabel)
                    <a href="{{ route('admin.leads.index', ['status' => $statusKey]) }}" style="color:inherit;text-decoration:none"><i style="background: {{ $pipelineColours[$statusKey] }}"></i>{{ $statusLabel }} <strong>{{ $pipeline[$statusKey] ?? 0 }}</strong></a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid-2" style="margin-top:18px">
        <div class="card">
            <div class="card-header">
                <h2>Follow-ups due</h2>
                <a class="btn btn-sm" href="{{ route('admin.leads.index', ['follow_up' => 1]) }}">View all</a>
            </div>
            @forelse ($followUps as $lead)
                <div style="display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px solid var(--line)">
                    <div><a class="row-link" href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a><span class="sub muted" style="display:block;font-size:12px">{{ $lead->product?->name ?? $lead->enquiryTypeLabel() }}</span></div>
                    <span class="badge {{ $lead->follow_up_at->isPast() && ! $lead->follow_up_at->isToday() ? 'badge-danger' : 'badge-warn' }}">{{ $lead->follow_up_at->isToday() ? 'Today' : $lead->follow_up_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="muted">Nothing due. Set a follow-up date on a lead and it will show up here.</p>
            @endforelse
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Latest leads</h2>
                <a class="btn btn-sm" href="{{ route('admin.leads.index') }}">All leads</a>
            </div>
            @forelse ($recentLeads as $lead)
                <div style="display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px solid var(--line)">
                    <div><a class="row-link" href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a><span class="muted" style="display:block;font-size:12px">{{ $lead->sourceLabel() }} · {{ $lead->created_at->diffForHumans() }}</span></div>
                    <span class="badge badge-{{ $lead->status }}">{{ $lead->statusLabel() }}</span>
                </div>
            @empty
                <p class="muted">No leads yet. When a WhatsApp chat turns into a real enquiry, log it from WhatsApp clicks or + Add lead to track it here.</p>
            @endforelse
        </div>
    </div>

    <div class="grid-2" style="margin-top:18px">
        <div class="card">
            <h2>SEO health</h2>
            @if (empty($seoIssues))
                <p class="muted">✓ No issues found. Nice work.</p>
            @else
                <ul class="issue-list">
                    @foreach ($seoIssues as $issue)
                        <li>
                            <details>
                                <summary>@if (! empty($issue['critical']))<span class="badge badge-danger">Important</span> @endif{{ $issue['label'] }} <span class="badge badge-warn">{{ $issue['items']->count() }}</span></summary>
                                <ul>
                                    @foreach ($issue['items'] as $item)
                                        <li><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
                                    @endforeach
                                </ul>
                            </details>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card">
            <h2>Most enquired products · 30 days</h2>
            @forelse ($topProducts as $product)
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--line)">
                    <a href="{{ route('admin.products.edit', $product) }}">{{ $product->name }}</a>
                    <strong>{{ $product->leads_count }}</strong>
                </div>
            @empty
                <p class="muted">No product enquiries in the last 30 days.</p>
            @endforelse
        </div>
    </div>
@endsection
