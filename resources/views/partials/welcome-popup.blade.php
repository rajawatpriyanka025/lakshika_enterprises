{{-- Welcome popup, managed in Admin → Settings → Welcome popup. Shown once per visitor per N days. --}}
@php
    $popupHeading = (string) setting('popup_heading');
    $popupMessage = (string) setting('popup_message');
    $popupImage = setting('popup_image');
    $popupButtonUrl = (string) setting('popup_button_url');
    $popupButtonUrl = str_starts_with($popupButtonUrl, '/') ? url($popupButtonUrl) : $popupButtonUrl;
    // Editing the copy changes this key, so visitors see the new message.
    $popupKey = 'le_welcome_'.hash('crc32b', $popupHeading.'|'.$popupMessage.'|'.$popupImage);
@endphp
<dialog class="welcome-popup" id="welcome-popup" aria-labelledby="welcome-popup-title"
    data-key="{{ $popupKey }}" data-delay="{{ max(0, (int) setting('popup_delay')) }}" data-days="{{ max(0, (int) setting('popup_frequency_days')) }}">
    <form method="dialog" class="welcome-popup-close-form">
        <button class="welcome-popup-close" value="close" aria-label="Close">×</button>
    </form>
    @if ($popupImage)
        <img class="welcome-popup-image" src="{{ asset('storage/'.$popupImage) }}" alt="" width="800" height="400" loading="lazy">
    @else
        <img class="welcome-popup-logo" src="{{ asset('images/brand/logo.png') }}" alt="" width="908" height="597" loading="lazy">
    @endif
    <div class="welcome-popup-body">
        <span class="eyebrow">HELLO &amp; WELCOME</span>
        <h2 id="welcome-popup-title">{{ $popupHeading }}</h2>
        @if ($popupMessage !== '')<p>{{ $popupMessage }}</p>@endif
        <div class="welcome-popup-actions">
            @if (setting('popup_button_label') && $popupButtonUrl)
                <a class="button button-dark" href="{{ $popupButtonUrl }}" data-popup-dismiss>{{ setting('popup_button_label') }} <span aria-hidden="true">↗</span></a>
            @endif
            @if (setting('popup_show_whatsapp') && setting('whatsapp_number'))
                <a class="button button-whatsapp" href="{{ route('whatsapp') }}" target="_blank" rel="nofollow noopener" data-popup-dismiss>Chat on WhatsApp</a>
            @endif
        </div>
        <form method="dialog"><button class="welcome-popup-later" value="later">Maybe later</button></form>
    </div>
</dialog>
<script>
(function () {
    var popup = document.getElementById('welcome-popup');
    if (!popup || typeof popup.showModal !== 'function' || navigator.webdriver) return;

    var key = popup.dataset.key;
    var days = parseInt(popup.dataset.days, 10) || 0;
    var store = days > 0 ? window.localStorage : window.sessionStorage;

    try {
        var seen = parseInt(store.getItem(key), 10);
        if (seen && (days === 0 || Date.now() - seen < days * 864e5)) return;
    } catch (e) {
        return; // Storage blocked: don't risk showing the popup on every page.
    }

    var remember = function () {
        try { store.setItem(key, String(Date.now())); } catch (e) {}
    };

    window.setTimeout(function () {
        if (document.querySelector('dialog[open]')) return;
        popup.showModal();
        remember();
    }, (parseInt(popup.dataset.delay, 10) || 0) * 1000);

    // Clicking the dimmed backdrop closes it.
    popup.addEventListener('click', function (event) {
        if (event.target === popup) popup.close();
    });

    popup.querySelectorAll('[data-popup-dismiss]').forEach(function (link) {
        link.addEventListener('click', function () { popup.close(); });
    });
})();
</script>
