(function () {
    'use strict';

    // Mobile sidebar
    document.querySelectorAll('[data-nav-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            var shell = document.querySelector('.admin-shell');
            var open = shell.classList.toggle('nav-open');
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });

    // Confirm destructive actions
    document.addEventListener('submit', function (event) {
        var message = event.target.getAttribute('data-confirm');
        if (message && !window.confirm(message)) {
            event.preventDefault();
        }
    });

    // Character counters: <input data-count="60">
    function updateCounter(input) {
        var counter = document.querySelector('[data-counter-for="' + input.id + '"]');
        if (!counter) return;
        var max = parseInt(input.getAttribute('data-count'), 10);
        var length = input.value.length;
        counter.textContent = length + ' / ' + max;
        counter.classList.toggle('is-over', length > max);
    }
    document.querySelectorAll('[data-count]').forEach(function (input) {
        updateCounter(input);
        input.addEventListener('input', function () { updateCounter(input); });
    });

    // Slug: fill from the name field until the slug is edited by hand
    var slug = document.querySelector('[data-slug-from]');
    if (slug) {
        var source = document.getElementById(slug.getAttribute('data-slug-from'));
        var touched = slug.value !== '';
        var slugify = function (value) {
            return value.toLowerCase().normalize('NFKD').replace(/[̀-ͯ]/g, '')
                .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        };
        slug.addEventListener('input', function () { touched = slug.value !== ''; });
        if (source) {
            source.addEventListener('input', function () {
                if (!touched) {
                    slug.value = slugify(source.value);
                    slug.dispatchEvent(new Event('change'));
                }
            });
        }
    }

    // Google search preview
    var serp = document.querySelector('[data-serp]');
    if (serp) {
        var titleEl = serp.querySelector('.serp-title');
        var descEl = serp.querySelector('.serp-desc');
        var urlEl = serp.querySelector('.serp-url');
        var suffix = serp.getAttribute('data-suffix');
        var base = serp.getAttribute('data-base');
        var fallbackTitle = document.getElementById(serp.getAttribute('data-title-fallback'));
        var fallbackDesc = document.getElementById(serp.getAttribute('data-desc-fallback'));
        var metaTitle = document.getElementById('meta_title');
        var metaDesc = document.getElementById('meta_description');
        var slugInput = document.getElementById('slug');

        var truncate = function (text, max) {
            text = text.replace(/\s+/g, ' ').trim();
            return text.length > max ? text.slice(0, max - 1).trim() + '…' : text;
        };

        var render = function () {
            var title = (metaTitle && metaTitle.value) || (fallbackTitle && fallbackTitle.value) || '';
            if (title && suffix && title.toLowerCase().indexOf(suffix.toLowerCase()) === -1) {
                title += ' | ' + suffix;
            }
            var desc = (metaDesc && metaDesc.value) || (fallbackDesc && fallbackDesc.value) || '';
            titleEl.textContent = truncate(title || 'Page title', 60);
            descEl.textContent = truncate(desc || 'Add a meta description to control the text shown in Google.', 160);
            if (slugInput && base) {
                urlEl.textContent = base + (slugInput.value || '…');
            }
        };

        [metaTitle, metaDesc, fallbackTitle, fallbackDesc, slugInput].forEach(function (input) {
            if (input) {
                input.addEventListener('input', render);
                input.addEventListener('change', render);
            }
        });
        render();
    }

    // Image preview before upload
    document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
        input.addEventListener('change', function () {
            var target = document.getElementById(input.getAttribute('data-preview'));
            if (!target || !input.files || !input.files[0]) return;
            target.src = URL.createObjectURL(input.files[0]);
            target.hidden = false;
        });
    });

    // Repeater (guide sections)
    document.querySelectorAll('[data-repeater]').forEach(function (repeater) {
        var list = repeater.querySelector('[data-repeater-list]');
        var template = repeater.querySelector('template');

        var renumber = function () {
            list.querySelectorAll('.repeater-item').forEach(function (item, index) {
                item.querySelector('.repeater-index').textContent = 'Section ' + (index + 1);
                item.querySelectorAll('[data-name]').forEach(function (field) {
                    field.name = field.getAttribute('data-name').replace('__INDEX__', index);
                });
            });
        };

        repeater.querySelector('[data-repeater-add]').addEventListener('click', function () {
            list.appendChild(template.content.cloneNode(true));
            renumber();
            var items = list.querySelectorAll('.repeater-item');
            var last = items[items.length - 1];
            if (last) last.querySelector('input, textarea').focus();
        });

        list.addEventListener('click', function (event) {
            var button = event.target.closest('[data-repeater-remove]');
            if (!button) return;
            if (list.querySelectorAll('.repeater-item').length === 1) {
                button.closest('.repeater-item').querySelectorAll('input, textarea').forEach(function (field) { field.value = ''; });
                return;
            }
            button.closest('.repeater-item').remove();
            renumber();
        });

        renumber();
    });
})();
