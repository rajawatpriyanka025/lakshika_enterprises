@extends('admin.layouts.app')

@section('title', $lead->name)
@section('subtitle', $lead->enquiryTypeLabel().' · received '.$lead->created_at->format('j M Y, g:i a').' ('.$lead->created_at->diffForHumans().')')

@section('actions')
    @if ($lead->whatsappUrl())<a class="btn btn-whatsapp" href="{{ $lead->whatsappUrl() }}" target="_blank" rel="noopener">WhatsApp</a>@endif
    @if ($lead->phone)<a class="btn" href="tel:{{ preg_replace('/[^0-9+]/', '', $lead->phone) }}">Call</a>@endif
    @if ($lead->email)<a class="btn" href="mailto:{{ $lead->email }}?subject={{ rawurlencode('Your enquiry with '.setting('site_name')) }}">Email</a>@endif
    <a class="btn" href="{{ route('admin.leads.index') }}">← All leads</a>
@endsection

@section('content')
    <div class="form-layout">
        <div class="stack">
            <div class="card">
                <h2>Enquiry</h2>
                <dl class="details">
                    <dt>Phone</dt><dd>{{ $lead->phone ?: '—' }}</dd>
                    <dt>Email</dt><dd>{{ $lead->email ?: '—' }}</dd>
                    <dt>City</dt><dd>{{ $lead->city ?: '—' }}</dd>
                    <dt>Type</dt><dd>{{ $lead->enquiryTypeLabel() }}</dd>
                    <dt>Product</dt><dd>@if ($lead->product)<a href="{{ route('products.show', $lead->product->slug) }}" target="_blank" rel="noopener">{{ $lead->product->name }} ↗</a>@else — @endif</dd>
                    <dt>Quantity</dt><dd>{{ $lead->quantity ?: '—' }}</dd>
                    <dt>Source</dt><dd>{{ $lead->sourceLabel() }}</dd>
                    @if ($lead->page_url)<dt>Sent from</dt><dd><a href="{{ $lead->page_url }}" target="_blank" rel="noopener noreferrer">{{ \Illuminate\Support\Str::after($lead->page_url, '://') }}</a></dd>@endif
                    @if ($lead->utm_source)<dt>Campaign</dt><dd>{{ collect([$lead->utm_source, $lead->utm_medium, $lead->utm_campaign])->filter()->implode(' / ') }}</dd>@endif
                </dl>
                @if ($lead->message)
                    <h2 style="margin-top:18px">Message</h2>
                    <div class="message-box">{{ $lead->message }}</div>
                @endif
            </div>

            <div class="card">
                <h2>Notes &amp; activity</h2>
                <form method="post" action="{{ route('admin.leads.notes.store', $lead) }}" style="margin-bottom:18px">
                    @csrf
                    @include('admin.partials.textarea', ['name' => 'body', 'label' => 'Add a note', 'rows' => 3, 'hint' => 'e.g. "Called, sent catalogue on WhatsApp, will confirm quantity Friday."'])
                    <button class="btn btn-primary" type="submit">Add note</button>
                </form>
                @if ($lead->notes->isNotEmpty())
                    <ul class="timeline">
                        @foreach ($lead->notes as $note)
                            <li>
                                <span class="meta">{{ $note->user?->name ?? 'System' }} · {{ $note->created_at->format('j M Y, g:i a') }}</span>
                                <p>{{ $note->body }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="muted">No notes yet.</p>
                @endif
            </div>
        </div>

        <aside>
            <form class="card" method="post" action="{{ route('admin.leads.update', $lead) }}">
                @csrf
                @method('PUT')
                <h2>Status</h2>
                @include('admin.partials.select', ['name' => 'status', 'label' => 'Stage', 'options' => \App\Models\Lead::STATUSES, 'value' => $lead->status])
                @include('admin.partials.field', ['name' => 'follow_up_at', 'label' => 'Follow up on', 'type' => 'date', 'value' => $lead->follow_up_at])
                @include('admin.partials.select', ['name' => 'enquiry_type', 'label' => 'Enquiry type', 'options' => \App\Models\Lead::ENQUIRY_TYPES, 'value' => $lead->enquiry_type])
                @include('admin.partials.select', ['name' => 'product_id', 'label' => 'Product', 'options' => $products, 'value' => $lead->product_id, 'placeholder' => '— None —'])
                @include('admin.partials.field', ['name' => 'quantity', 'label' => 'Quantity', 'type' => 'number', 'value' => $lead->quantity, 'attrs' => 'min="1"'])
                @include('admin.partials.select', ['name' => 'source', 'label' => 'Source', 'options' => \App\Models\Lead::SOURCES, 'value' => $lead->source])
                <button class="btn btn-primary btn-block" type="submit">Save</button>
            </form>

            <div class="card">
                <h2>Contact</h2>
                @if ($lead->customer)
                    <p style="margin-top:0"><a class="row-link" href="{{ route('admin.customers.show', $lead->customer) }}">{{ $lead->customer->name }}</a><br>
                        <span class="badge">{{ $lead->customer->typeLabel() }}</span>
                        @if ($lead->customer->company)<span class="muted"> · {{ $lead->customer->company }}</span>@endif
                    </p>
                    @if ($lead->customer->leads->count() > 1)
                        <p class="muted">{{ $lead->customer->leads->count() }} enquiries in total.</p>
                    @endif
                    <a class="btn btn-sm" href="{{ route('admin.customers.edit', $lead->customer) }}">Edit contact</a>
                @else
                    <p class="muted mt-0">Not linked to a contact.</p>
                @endif
            </div>

            <div class="card">
                @include('admin.partials.delete', ['action' => route('admin.leads.destroy', $lead), 'confirm' => 'Delete this lead and its notes?', 'label' => 'Delete lead'])
            </div>
        </aside>
    </div>
@endsection
