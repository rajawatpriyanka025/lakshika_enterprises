<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'quoted' => 'Quote sent',
        'won' => 'Won',
        'lost' => 'Lost',
    ];

    public const SOURCES = [
        'contact_form' => 'Contact form',
        'product_enquiry' => 'Product enquiry',
        'phone' => 'Phone call',
        'whatsapp' => 'WhatsApp',
        'marketplace' => 'Marketplace',
        'referral' => 'Referral',
        'other' => 'Other',
    ];

    public const ENQUIRY_TYPES = [
        'general' => 'General question',
        'product' => 'Product question',
        'bulk' => 'Bulk / wholesale order',
        'dealer' => 'Become a dealer',
        'corporate' => 'Corporate gifting',
    ];

    protected $fillable = [
        'customer_id', 'product_id', 'name', 'email', 'phone', 'city', 'enquiry_type', 'quantity', 'message',
        'source', 'status', 'follow_up_at', 'page_url', 'referrer', 'utm_source', 'utm_medium', 'utm_campaign', 'ip_address',
    ];

    protected function casts(): array
    {
        return ['follow_up_at' => 'datetime'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['won', 'lost']);
    }

    public function scopeFollowUpDue(Builder $query): Builder
    {
        return $query->open()->whereNotNull('follow_up_at')->where('follow_up_at', '<=', now()->endOfDay());
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function sourceLabel(): string
    {
        return self::SOURCES[$this->source] ?? ucfirst(str_replace('_', ' ', $this->source));
    }

    public function enquiryTypeLabel(): string
    {
        return self::ENQUIRY_TYPES[$this->enquiry_type] ?? ucfirst($this->enquiry_type);
    }

    public function whatsappUrl(): ?string
    {
        $phone = Customer::normalisePhone($this->phone);

        if (! $phone) {
            return null;
        }

        return 'https://wa.me/'.(strlen($phone) === 10 ? '91'.$phone : $phone);
    }
}
