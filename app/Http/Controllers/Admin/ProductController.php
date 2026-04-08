<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function mapProduct($product): array
    {
        $data = is_array($product) ? $product : $product->toArray();
        $data['storeId']      = $data['store_id'];
        $data['categoryId']   = $data['category_id'];
        $data['affiliateUrl'] = $data['affiliate_url'];
        $data['imageUrl']     = $data['image_url'];
        $data['isTrending']   = (bool)$data['is_trending'];
        $data['originalPrice'] = $data['original_price'];
        $data['discountedPrice'] = $data['discounted_price'];
        return $data;
    }

    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $products = $query->get()->map(fn($p) => $this->mapProduct($p));
        return response()->json($products);
    }

    public function show($id)
    {
        return response()->json($this->mapProduct(Product::findOrFail($id)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string',
            'store_id'     => 'required|string',
            'category_id'  => 'required|string',
            'description'  => 'required|string',
            'affiliate_url'=> 'required|string',
            'image_url'    => 'nullable|string',
            'is_trending'  => 'required|boolean',
            'countries'    => 'required|array',
            'original_price' => 'nullable|string',
            'discounted_price' => 'nullable|string',
        ]);

        $product = Product::create($validated);

        return response()->json($this->mapProduct($product->fresh()), 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'title'        => 'sometimes|string',
            'store_id'     => 'sometimes|string',
            'category_id'  => 'sometimes|string',
            'description'  => 'sometimes|string',
            'affiliate_url'=> 'sometimes|string',
            'image_url'    => 'nullable|string',
            'is_trending'  => 'sometimes|boolean',
            'countries'    => 'sometimes|array',
            'original_price' => 'nullable|string',
            'discounted_price' => 'nullable|string',
        ]);

        $product->update($validated);
        return response()->json($this->mapProduct($product->fresh()));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:application/json,text/csv,text/plain'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $importedCount = 0;
        $errors = [];
        $data = [];

        if ($extension === 'json') {
            $parsed = json_decode(file_get_contents($file->path()), true);
            if (!is_array($parsed)) {
                return response()->json(['message' => 'Invalid JSON format'], 400);
            }
            $data = $parsed;
        } elseif (in_array($extension, ['csv', 'txt'])) {
            $handle = fopen($file->path(), "r");
            // Remove BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") rewind($handle);
            
            $header = fgetcsv($handle, 10000, ",");
            if ($header) {
                while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
                    if (count($header) === count($row)) {
                        $data[] = array_combine($header, $row);
                    }
                }
            }
            fclose($handle);
        } else {
            return response()->json(['message' => 'Unsupported file format, use JSON or CSV'], 400);
        }

        foreach ($data as $index => $row) {
            try {
                // Ensure required fields
                if (empty($row['store_id']) || empty($row['category_id']) || empty($row['title']) || empty($row['description']) || empty($row['affiliate_url'])) {
                    throw new \Exception("Missing required fields");
                }

                Product::create([
                    'store_id' => $row['store_id'],
                    'category_id' => $row['category_id'],
                    'title' => $row['title'],
                    'original_price' => $row['original_price'] ?? null,
                    'discounted_price' => $row['discounted_price'] ?? null,
                    'description' => $row['description'],
                    'affiliate_url' => $row['affiliate_url'],
                    'image_url' => $row['image_url'] ?? null,
                    'is_trending' => filter_var($row['is_trending'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'countries' => isset($row['countries']) ? (is_array($row['countries']) ? $row['countries'] : array_map('trim', explode(',', $row['countries']))) : ['us']
                ]);
                $importedCount++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'message' => "Successfully imported $importedCount products.",
            'importedCount' => $importedCount,
            'errors' => $errors
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
