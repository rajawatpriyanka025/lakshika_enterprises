@extends('admin.layouts.app')

@section('title', $faq->exists ? 'Edit FAQ' : 'Add FAQ')

@section('actions')
    <a class="btn" href="{{ route('admin.faqs.index') }}">← All FAQs</a>
@endsection

@section('content')
    <form class="card" method="post" action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" style="max-width:760px">
        @csrf
        @if ($faq->exists) @method('PUT') @endif
        @include('admin.partials.field', ['name' => 'question', 'label' => 'Question', 'value' => $faq->question, 'required' => true, 'hint' => 'Phrase it the way a customer would ask it.'])
        @include('admin.partials.textarea', ['name' => 'answer', 'label' => 'Answer', 'value' => $faq->answer, 'required' => true, 'rows' => 6, 'hint' => 'Answer directly in the first sentence. Leave a blank line between paragraphs.'])
        <div class="grid-2">
            @include('admin.partials.field', ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'value' => $faq->sort_order, 'attrs' => 'min="0"'])
            <div style="padding-top:30px">@include('admin.partials.checkbox', ['name' => 'is_active', 'label' => 'Show on website', 'checked' => $faq->is_active])</div>
        </div>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Save FAQ</button></div>
    </form>
@endsection
