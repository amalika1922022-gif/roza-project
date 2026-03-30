<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductAdminController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('Admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('Admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // ✅ نفس قواعد التحقق تبعك، بس عبر Validator لنقدر نرجّع JSON بدون Reload
        $validator = Validator::make(
            $request->all(),
            [
                'name'          => ['bail', 'required', 'string', 'max:191'],
                'slug'          => ['bail', 'nullable', 'string', 'max:191', 'unique:products,slug'],
                'category_id'   => ['bail', 'required', 'exists:categories,id'],

                // price: required + numbers only (decimal allowed)
                'price'         => ['bail', 'required', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0'],

                // compare_price: optional + numbers only
                'compare_price' => ['bail', 'nullable', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0'],

                // stock: required integer numbers only
                'stock'         => ['bail', 'required', 'integer', 'min:0'],

                // weight: optional numbers only
                'weight'        => ['bail', 'nullable', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0'],

                'description'   => ['bail', 'nullable', 'string'],
                'is_active'     => ['bail', 'required', 'boolean'],
                'images'        => ['bail', 'nullable', 'array'],
                'images.*'      => ['bail', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
            ],
            [
                'name.required'        => 'Product name is required.',
                'category_id.required' => 'Please select a category.',
                'category_id.exists'   => 'Invalid category selected.',

                'price.required' => 'Price is required.',
                'price.regex'    => 'Price must be numbers only.',
                'price.min'      => 'Price must be 0 or more.',

                'compare_price.regex' => 'Compare price must be numbers only.',
                'compare_price.min'   => 'Compare price must be 0 or more.',

                'stock.required' => 'Stock is required.',
                'stock.integer'  => 'Stock must be a whole number.',
                'stock.min'      => 'Stock must be 0 or more.',

                'weight.regex' => 'Weight must be numbers only.',
                'weight.min'   => 'Weight must be 0 or more.',
            ]
        );

        if ($validator->fails()) {
            // ✅ لو AJAX: رجّع أخطاء JSON بدون Redirect
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // ✅ لو submit عادي: نفس سلوك Laravel
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if (empty($data['slug'])) {
            $baseName     = $data['name'] ?? $request->input('name', '');
            $data['slug'] = Str::slug($baseName);
        }

        DB::transaction(function () use ($data, $request) {
            $product = Product::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('product_images', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'file_path'  => $path,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }
            }
        });

        // ✅ لو AJAX: رجّع redirect URL بدل Redirect فعلي
        if ($request->expectsJson()) {
            return response()->json([
                'message'  => 'Product created successfully.',
                'redirect' => route('admin.products.index'),
            ]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images']);

        return view('Admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $product->load('images');

        return view('Admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(
            [
                'name'          => ['bail', 'required', 'string', 'max:191'],
                'slug'          => ['bail', 'nullable', 'string', 'max:191', 'unique:products,slug,' . $product->id],
                'category_id'   => ['bail', 'required', 'exists:categories,id'],

                'price'         => ['bail', 'required', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0'],
                'compare_price' => ['bail', 'nullable', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0'],
                'stock'         => ['bail', 'required', 'integer', 'min:0'],
                'weight'        => ['bail', 'nullable', 'regex:/^\d+(\.\d{1,2})?$/', 'min:0'],

                'description'   => ['bail', 'nullable', 'string'],
                'is_active'     => ['bail', 'required', 'boolean'],
                'images'        => ['bail', 'nullable', 'array'],
                'images.*'      => ['bail', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],

                'primary_image_id'        => ['bail', 'nullable', 'integer'],
                'primary_new_image_index' => ['bail', 'nullable', 'integer', 'min:0'],
                'deleted_image_ids'       => ['bail', 'nullable', 'string'],
            ],
            [
                'name.required'        => 'Product name is required.',
                'category_id.required' => 'Please select a category.',
                'category_id.exists'   => 'Invalid category selected.',

                'price.required' => 'Price is required.',
                'price.regex'    => 'Price must be numbers only.',
                'price.min'      => 'Price must be 0 or more.',

                'compare_price.regex' => 'Compare price must be numbers only.',
                'compare_price.min'   => 'Compare price must be 0 or more.',

                'stock.required' => 'Stock is required.',
                'stock.integer'  => 'Stock must be a whole number.',
                'stock.min'      => 'Stock must be 0 or more.',

                'weight.regex' => 'Weight must be numbers only.',
                'weight.min'   => 'Weight must be 0 or more.',
            ]
        );

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        DB::transaction(function () use ($data, $request, $product) {
            $baseData = $data;
            unset($baseData['images'], $baseData['primary_image_id'], $baseData['primary_new_image_index'], $baseData['deleted_image_ids']);
            $baseData['is_active'] = $request->boolean('is_active');
            $product->update($baseData);

            $primaryImageId       = $request->input('primary_image_id');
            $primaryNewImageIndex = $request->input('primary_new_image_index');
            $deletedImageIds      = collect(
                array_filter(explode(',', (string) $request->input('deleted_image_ids')))
            )->unique();

            if ($deletedImageIds->isNotEmpty()) {
                $imagesToDelete = $product->images()
                    ->whereIn('id', $deletedImageIds)
                    ->get();

                foreach ($imagesToDelete as $img) {
                    if ($img->file_path && Storage::disk('public')->exists($img->file_path)) {
                        Storage::disk('public')->delete($img->file_path);
                    }
                    $img->delete();
                }
            }

            $uploadedImages = $request->file('images', []);
            $createdImages  = [];

            if (!empty($uploadedImages)) {
                $currentMaxSort = $product->images()->max('sort_order') ?? 0;

                foreach ($uploadedImages as $index => $file) {
                    $path = $file->store('product_images', 'public');

                    $img = ProductImage::create([
                        'product_id' => $product->id,
                        'file_path'  => $path,
                        'is_primary' => false,
                        'sort_order' => $currentMaxSort + $index + 1,
                    ]);

                    $createdImages[$index] = $img;
                }
            }

            $product->images()->update(['is_primary' => false]);

            if ($primaryImageId) {
                $product->images()
                    ->where('id', $primaryImageId)
                    ->update(['is_primary' => true]);
            } elseif ($primaryNewImageIndex !== null && $primaryNewImageIndex !== '') {
                $idx = (int) $primaryNewImageIndex;

                if (array_key_exists($idx, $createdImages)) {
                    $createdImages[$idx]->update(['is_primary' => true]);
                }
            } else {
                $firstImage = $product->images()->first();
                if ($firstImage) {
                    $firstImage->update(['is_primary' => true]);
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->load('images');

        foreach ($product->images as $image) {
            if ($image->file_path && Storage::disk('public')->exists($image->file_path)) {
                Storage::disk('public')->delete($image->file_path);
            }
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function setPrimaryImage($productId, $imageId)
    {
        $product = Product::with('images')->findOrFail($productId);
        $image   = $product->images()->where('id', $imageId)->firstOrFail();

        DB::transaction(function () use ($product, $image) {
            $product->images()->update(['is_primary' => false]);
            $image->is_primary = true;
            $image->save();
        });

        return redirect()
            ->route('admin.products.edit', $product->id)
            ->with('success', 'Primary image updated successfully.');
    }

    public function deleteImage($productId, $imageId)
    {
        $product = Product::with('images')->findOrFail($productId);
        $image   = $product->images()->where('id', $imageId)->firstOrFail();

        if ($image->file_path && Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }

        $image->delete();

        if ($image->is_primary) {
            $next = $product->images()->first();
            if ($next) {
                $next->is_primary = true;
                $next->save();
            }
        }

        return redirect()
            ->route('admin.products.edit', $productId)
            ->with('success', 'Image deleted successfully.');
    }
}
