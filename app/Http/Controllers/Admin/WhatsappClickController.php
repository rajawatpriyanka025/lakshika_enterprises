<?php

namespace App\Http\Controllers\Admin;

use App\Models\WhatsappClick;
use Illuminate\Contracts\View\View;

class WhatsappClickController extends AdminController
{
    public function __invoke(): View
    {
        $since = now()->subDays(30);

        return view('admin.whatsapp-clicks.index', [
            'clicks' => WhatsappClick::query()->with('product')->latest('created_at')->paginate(50),
            'total30' => WhatsappClick::query()->where('created_at', '>=', $since)->count(),
            'unique30' => WhatsappClick::query()->where('created_at', '>=', $since)->distinct()->count('ip_hash'),
            'byTopic' => WhatsappClick::query()
                ->where('created_at', '>=', $since)
                ->whereNotNull('topic')
                ->selectRaw('topic, count(*) as total')
                ->groupBy('topic')
                ->orderByDesc('total')
                ->get(),
            'byProduct' => WhatsappClick::query()
                ->where('created_at', '>=', $since)
                ->selectRaw('product_id, count(*) as total')
                ->groupBy('product_id')
                ->orderByDesc('total')
                ->with('product')
                ->limit(10)
                ->get(),
        ]);
    }
}
