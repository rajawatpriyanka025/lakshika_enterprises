@extends('admin.layouts.app')

@section('title', $customer->name)
@section('subtitle', $customer->typeLabel().($customer->company ? ' · '.$customer->company : '').($customer->city ? ' · '.$customer->city : ''))

@section('actions')
    @if ($customer->phone)
        <a class="btn btn-whatsapp" href="https://wa.me/{{ strlen($customer->phone) === 10 ? '91'.$customer->phone : $customer->phone }}" target="_blank" rel="noopener">WhatsApp</a>
        <a class="btn" href="tel:{{ $customer->phone }}">Call</a>
    @endif
    <a class="btn" href="{{ route('admin.leads.create', ['customer' => $customer->id]) }}">+ New enquiry</a>
    <a class="btn btn-primary" href="{{ route('admin.customers.edit', $customer) }}">Edit</a>
@endsection

@section('content')
    <div class="form-layout">
        <div class="stack">
            <div class="card">
                <h2>Enquiry history</h2>
                @if ($customer->leads->isEmpty())
                    <p class="muted">No enquiries yet.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Date</th><th>Enquiry</th><th>Source</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach ($customer->leads as $lead)
                                    <tr>
                                        <td><a class="row-link" href="{{ route('admin.leads.show', $lead) }}">{{ $lead->created_at->format('j M Y') }}</a></td>
                                        <td>{{ $lead->enquiryTypeLabel() }}<span class="sub">{{ $lead->product?->name }}{{ $lead->quantity ? ' · Qty '.$lead->quantity : '' }}</span></td>
                                        <td>{{ $lead->sourceLabel() }}</td>
                                        <td><span class="badge badge-{{ $lead->status }}">{{ $lead->statusLabel() }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @if ($customer->notes)
                <div class="card"><h2>Notes</h2><div class="message-box">{{ $customer->notes }}</div></div>
            @endif
        </div>
        <aside>
            <div class="card">
                <h2>Details</h2>
                <dl class="details">
                    <dt>Phone</dt><dd>{{ $customer->phone ?: '—' }}</dd>
                    <dt>Email</dt><dd>{{ $customer->email ?: '—' }}</dd>
                    <dt>City</dt><dd>{{ collect([$customer->city, $customer->state])->filter()->implode(', ') ?: '—' }}</dd>
                    <dt>GSTIN</dt><dd>{{ $customer->gst_number ?: '—' }}</dd>
                    @if ($customer->address)<dt>Address</dt><dd style="white-space:pre-line">{{ $customer->address }}</dd>@endif
                    <dt>Added</dt><dd>{{ $customer->created_at->format('j M Y') }}</dd>
                </dl>
            </div>
            <div class="card">
                @include('admin.partials.delete', ['action' => route('admin.customers.destroy', $customer), 'confirm' => 'Delete this contact? Their enquiries are kept.', 'label' => 'Delete contact'])
            </div>
        </aside>
    </div>
@endsection
