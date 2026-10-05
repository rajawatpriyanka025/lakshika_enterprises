<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends AdminController
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::query()->withCount('products')->ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category(['is_active' => true, 'sort_order' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = new Category;
        $this->save($request, $category);

        return redirect()->route('admin.categories.edit', $category)->with('status', 'Collection created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->save($request, $category);

        return redirect()->route('admin.categories.edit', $category)->with('status', 'Collection saved.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'Move or delete the products in this collection first.']);
        }

        $this->deleteImage($category->image_path);
        $this->deleteImage($category->og_image);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Collection deleted.');
    }

    private function save(Request $request, Category $category): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash', Rule::unique('categories', 'slug')->ignore($category)],
            'description' => ['required', 'string', 'max:5000'],
            'image' => self::IMAGE_RULES,
            'remove_image' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
        ] + $this->seoRules());

        $category->fill([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? null,
            'description' => $data['description'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'image_path' => $this->replaceImage($request, 'image', 'remove_image', $category->image_path, 'categories'),
        ] + $this->seoData($request, $data, $category->og_image));

        $category->save();
    }
}
