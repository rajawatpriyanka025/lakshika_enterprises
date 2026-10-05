<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappClick extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['product_id', 'topic', 'page_url', 'referrer', 'utm_source', 'ip_hash', 'user_agent'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function topicLabel(): string
    {
        return Lead::ENQUIRY_TYPES[$this->topic] ?? 'General';
    }
}
