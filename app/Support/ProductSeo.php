<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Str;

/**
 * Generates default SEO fields for products and scores how well each product is optimised.
 */
class ProductSeo
{
    public const TITLE_MAX = 60;

    public const DESCRIPTION_MIN = 120;

    public const DESCRIPTION_MAX = 160;

    public const MIN_WORDS = 150;

    public static function defaultTitle(Product $product): string
    {
        return Str::limit($product->name, self::TITLE_MAX, '');
    }

    /** The excerpt, topped up with the collection and brand when short, cut at a word boundary. */
    public static function defaultDescription(Product $product): string
    {
        $text = rtrim(trim((string) $product->excerpt), '.').'.';

        if (mb_strlen($text) < self::DESCRIPTION_MIN) {
            $collection = $product->category?->name ? strtolower($product->category->name) : 'our collection';
            $text .= " Explore {$collection} from ".setting('site_name').' and find it on Amazon, Flipkart or Meesho.';
        }

        if (mb_strlen($text) <= self::DESCRIPTION_MAX) {
            return $text;
        }

        $cut = mb_substr($text, 0, self::DESCRIPTION_MAX - 1);

        return rtrim(mb_substr($cut, 0, (int) mb_strrpos($cut, ' ')), ' ,;:-').'…';
    }

    /** Fill any empty SEO fields. Returns true when something changed. */
    public static function fillMissing(Product $product): bool
    {
        $product->loadMissing('category');

        if (blank($product->meta_title)) {
            $product->meta_title = static::defaultTitle($product);
        }

        if (blank($product->meta_description)) {
            $product->meta_description = static::defaultDescription($product);
        }

        if (blank($product->image_alt) && $product->image_path) {
            $product->image_alt = $product->name;
        }

        return $product->isDirty(['meta_title', 'meta_description', 'image_alt']);
    }

    /**
     * Checklist of on-page SEO checks.
     *
     * @return list<array{label: string, pass: bool, hint: string}>
     */
    public static function audit(Product $product, ?array $titleCounts = null): array
    {
        $titleCounts ??= static::titleCounts();
        $keyword = Str::lower(trim((string) $product->focus_keyword));
        $title = (string) ($product->meta_title ?: $product->name);
        $description = (string) $product->meta_description;
        $body = strip_tags((string) $product->description);
        $firstParagraph = Str::before(str_replace("\r\n", "\n", $body), "\n\n");
        $words = str_word_count($body);
        $contains = fn (string $haystack) => $keyword !== '' && str_contains(Str::lower($haystack), $keyword);

        // How many *other* products share this title.
        $storedTitle = Str::lower((string) ($product->getOriginal('meta_title') ?: $product->getOriginal('name')));
        $sameTitle = ($titleCounts[Str::lower($title)] ?? 0) - ($product->exists && $storedTitle === Str::lower($title) ? 1 : 0);

        return [
            [
                'label' => 'Focus keyword set',
                'pass' => $keyword !== '',
                'hint' => 'The main phrase people search for, e.g. "wooden wall clock" or "brass table lamp".',
            ],
            [
                'label' => 'Keyword in meta title',
                'pass' => $contains($title),
                'hint' => 'Put the keyword near the start of the meta title.',
            ],
            [
                'label' => 'Keyword in meta description',
                'pass' => $contains($description),
                'hint' => 'Google bolds matching words in the description.',
            ],
            [
                'label' => 'Keyword in product name (H1)',
                'pass' => $contains($product->name),
                'hint' => 'The page heading should include the keyword or close to it.',
            ],
            [
                'label' => 'Keyword in URL',
                'pass' => $keyword !== '' && str_contains((string) $product->slug, Str::slug($keyword)),
                'hint' => 'e.g. /products/'.(Str::slug($keyword) ?: 'your-keyword').'. Changing the URL adds a redirect automatically.',
            ],
            [
                'label' => 'Keyword in first paragraph',
                'pass' => $contains($firstParagraph),
                'hint' => 'Mention the keyword naturally in the opening sentences of the description.',
            ],
            [
                'label' => 'Meta title length ('.mb_strlen($title).' / '.self::TITLE_MAX.')',
                'pass' => mb_strlen($title) >= 20 && mb_strlen($title) <= self::TITLE_MAX,
                'hint' => 'Between 20 and 60 characters so it is not cut off in Google.',
            ],
            [
                'label' => 'Meta description length ('.mb_strlen($description).' / '.self::DESCRIPTION_MAX.')',
                'pass' => mb_strlen($description) >= self::DESCRIPTION_MIN && mb_strlen($description) <= self::DESCRIPTION_MAX,
                'hint' => 'Between 120 and 160 characters.',
            ],
            [
                'label' => 'Unique meta title',
                'pass' => $sameTitle <= 0,
                'hint' => 'Another product uses the same meta title.',
            ],
            [
                'label' => "Description length ({$words} words)",
                'pass' => $words >= self::MIN_WORDS,
                'hint' => 'At least '.self::MIN_WORDS.' words: what it is, size, material, finish, where it works best, care.',
            ],
            [
                'label' => 'Product photo',
                'pass' => filled($product->image_path),
                'hint' => 'Products with photos appear in Google Images and get more clicks.',
            ],
            [
                'label' => 'Photo alt text',
                'pass' => filled($product->image_path) && filled($product->image_alt),
                'hint' => 'Describe the photo, ideally including the keyword.',
            ],
            [
                'label' => 'Specifications filled',
                'pass' => count($product->specifications()) >= 3,
                'hint' => 'Add at least three of material, colour, size, weight and SKU.',
            ],
            [
                'label' => 'Visible to search engines',
                'pass' => ! $product->noindex && $product->is_active,
                'hint' => 'The product is hidden or set to noindex.',
            ],
        ];
    }

    /** @return array<string, int> lower-cased effective meta title => number of products using it */
    public static function titleCounts(): array
    {
        return Product::query()
            ->get(['name', 'meta_title'])
            ->countBy(fn (Product $product) => Str::lower($product->meta_title ?: $product->name))
            ->all();
    }

    public static function score(array $audit): int
    {
        return $audit === [] ? 0 : (int) round(collect($audit)->where('pass', true)->count() / count($audit) * 100);
    }

    public static function grade(int $score): string
    {
        return match (true) {
            $score >= 80 => 'good',
            $score >= 50 => 'ok',
            default => 'poor',
        };
    }
}
