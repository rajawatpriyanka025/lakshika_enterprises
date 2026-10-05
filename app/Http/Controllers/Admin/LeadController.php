<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends AdminController
{
    public function index(Request $request): View
    {
        return view('admin.leads.index', [
            'leads' => $this->filtered($request)->with(['product', 'customer'])->latest()->paginate(25)->withQueryString(),
            'counts' => Lead::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'filters' => $request->only(['q', 'status', 'source', 'type', 'follow_up']),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.leads.create', [
            // Query parameters let "Log as lead" on a WhatsApp click pre-fill the form.
            'lead' => new Lead([
                'source' => array_key_exists((string) $request->query('source'), Lead::SOURCES) ? $request->query('source') : 'whatsapp',
                'status' => 'new',
                'enquiry_type' => array_key_exists((string) $request->query('type'), Lead::ENQUIRY_TYPES) ? $request->query('type') : 'general',
                'product_id' => $request->integer('product') ?: null,
                'customer_id' => $request->integer('customer') ?: null,
            ]),
            'products' => Product::query()->orderBy('name')->pluck('name', 'id'),
            'customer' => $request->filled('customer') ? Customer::query()->find($request->integer('customer')) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules() + [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'city' => ['nullable', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:5000'],
            'customer_id' => ['nullable', 'exists:customers,id'],
        ]);

        $data['customer_id'] ??= Customer::matchOrCreate($data)->id;

        $lead = Lead::query()->create($data);

        return redirect()->route('admin.leads.show', $lead)->with('status', 'Lead added.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['product', 'customer.leads', 'notes.user']);

        return view('admin.leads.show', [
            'lead' => $lead,
            'products' => Product::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $previous = $lead->status;

        $lead->update($data);

        if ($previous !== $lead->status) {
            $lead->notes()->create([
                'user_id' => $request->user()->id,
                'body' => 'Status changed from '.(Lead::STATUSES[$previous] ?? $previous).' to '.$lead->statusLabel().'.',
            ]);
        }

        return back()->with('status', 'Lead updated.');
    }

    public function addNote(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $lead->notes()->create($data + ['user_id' => $request->user()->id]);

        if ($lead->status === 'new') {
            $lead->update(['status' => 'contacted']);
        }

        return back()->with('status', 'Note added.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', 'Lead deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        $leads = $this->filtered($request)->with(['product', 'customer'])->latest()->cursor();

        return response()->streamDownload(function () use ($leads) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel opens it correctly.
            fputcsv($out, ['ID', 'Date', 'Name', 'Phone', 'Email', 'City', 'Type', 'Product', 'Quantity', 'Source', 'Status', 'Follow up', 'Customer type', 'Message', 'UTM source', 'UTM campaign']);

            foreach ($leads as $lead) {
                fputcsv($out, array_map([$this, 'csvSafe'], [
                    $lead->id,
                    $lead->created_at->format('Y-m-d H:i'),
                    $lead->name,
                    $lead->phone,
                    $lead->email,
                    $lead->city,
                    $lead->enquiryTypeLabel(),
                    $lead->product?->name,
                    $lead->quantity,
                    $lead->sourceLabel(),
                    $lead->statusLabel(),
                    $lead->follow_up_at?->format('Y-m-d'),
                    $lead->customer?->typeLabel(),
                    $lead->message,
                    $lead->utm_source,
                    $lead->utm_campaign,
                ]));
            }

            fclose($out);
        }, 'leads-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Stop spreadsheet formula injection from visitor-supplied text. */
    public function csvSafe(mixed $value): mixed
    {
        return is_string($value) && preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }

    private function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(Lead::STATUSES))],
            'source' => ['required', Rule::in(array_keys(Lead::SOURCES))],
            'enquiry_type' => ['required', Rule::in(array_keys(Lead::ENQUIRY_TYPES))],
            'product_id' => ['nullable', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'follow_up_at' => ['nullable', 'date'],
        ];
    }

    private function filtered(Request $request): Builder
    {
        return Lead::query()
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn (Builder $query) => $query->where('name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('message', 'like', $term));
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->input('status')))
            ->when($request->filled('source'), fn (Builder $query) => $query->where('source', $request->input('source')))
            ->when($request->filled('type'), fn (Builder $query) => $query->where('enquiry_type', $request->input('type')))
            ->when($request->boolean('follow_up'), fn (Builder $query) => $query->followUpDue());
    }
}
