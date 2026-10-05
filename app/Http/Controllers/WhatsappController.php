<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Product;
use App\Models\WhatsappClick;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * All website enquiries go through WhatsApp: log the click for the CRM,
 * then open a chat with a message pre-filled with the product and topic.
 */
class WhatsappController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $number = preg_replace('/\D+/', '', (string) setting('whatsapp_number'));

        abort_if($number === '', 404);

        $product = $request->filled('product')
            ? Product::query()->active()->where('slug', $request->query('product'))->first()
            : null;

        $topic = array_key_exists((string) $request->query('type'), Lead::ENQUIRY_TYPES) ? $request->query('type') : null;
        $topic ??= $product ? 'product' : null;

        if (! $this->looksLikeBot($request)) {
            WhatsappClick::query()->create([
                'product_id' => $product?->id,
                'topic' => $topic,
                'page_url' => mb_substr((string) $request->headers->get('referer'), 0, 255) ?: null,
                'utm_source' => $request->session()->get('utm.source'),
                'ip_hash' => hash('sha256', $request->ip().config('app.key')),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            ]);
        }

        return redirect()->away('https://wa.me/'.$number.'?'.http_build_query(['text' => $this->message($product, $topic)]));
    }

    private function message(?Product $product, ?string $topic): string
    {
        $lines = [trim((string) setting('whatsapp_message'))];

        if ($topic && $topic !== 'general' && $topic !== 'product') {
            $lines[] = 'Enquiry: '.Lead::ENQUIRY_TYPES[$topic];
        }

        if ($product) {
            $lines[] = 'Product: '.$product->name."\n".route('products.show', $product->slug);
        }

        if (in_array($topic, ['bulk', 'corporate'], true)) {
            $lines[] = "Quantity:\nCity:";
        } elseif ($topic === 'dealer') {
            $lines[] = "Shop / company name:\nCity:";
        }

        return implode("\n\n", array_filter($lines));
    }

    private function looksLikeBot(Request $request): bool
    {
        return (bool) preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp/i', (string) $request->userAgent());
    }
}
