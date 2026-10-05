<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    public const TYPES = [
        'retail' => 'Retail buyer',
        'wholesale' => 'Wholesale buyer',
        'dealer' => 'Dealer / reseller',
        'corporate' => 'Corporate / gifting',
        'interior' => 'Interior designer',
    ];

    protected $fillable = ['name', 'email', 'phone', 'company', 'type', 'city', 'state', 'gst_number', 'address', 'notes'];

    protected static function booted(): void
    {
        static::saving(function (self $customer) {
            $customer->phone = static::normalisePhone($customer->phone);
            $customer->email = $customer->email ? strtolower(trim($customer->email)) : null;
        });
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class)->latest();
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /** Store Indian mobile numbers as 10 digits so the same buyer always matches. */
    public static function normalisePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return substr($digits, 2);
        }

        return strlen($digits) === 11 && str_starts_with($digits, '0') ? substr($digits, 1) : $digits;
    }

    /** Find an existing contact by phone or email, or create one from the enquiry. */
    public static function matchOrCreate(array $attributes): self
    {
        $phone = static::normalisePhone($attributes['phone'] ?? null);
        $email = filled($attributes['email'] ?? null) ? strtolower(trim($attributes['email'])) : null;

        $customer = ($phone || $email) ? static::query()
            ->where(function ($query) use ($phone, $email) {
                $query->when($phone, fn ($query) => $query->orWhere('phone', $phone))
                    ->when($email, fn ($query) => $query->orWhere('email', $email));
            })
            ->first() : null;

        if ($customer) {
            $customer->fill([
                'phone' => $customer->phone ?: $phone,
                'email' => $customer->email ?: $email,
                'city' => $customer->city ?: ($attributes['city'] ?? null),
            ])->save();

            return $customer;
        }

        return static::query()->create([
            'name' => $attributes['name'],
            'phone' => $phone,
            'email' => $email,
            'city' => $attributes['city'] ?? null,
            'type' => $attributes['type'] ?? 'retail',
        ]);
    }
}
