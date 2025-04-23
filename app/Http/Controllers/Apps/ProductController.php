namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get products with optional search
        $products = Product::when(request()->q, function($query) {
            return $query->where('title', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        // Return inertia view with products
        return Inertia::render('Apps/Products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get categories for the product
        $categories = Category::all();

        // Return inertia view with categories
        return Inertia::render('Apps/Products/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the product data
        $this->validateProduct($request);

        // Upload and store the image
        $image = $this->storeImage($request);

        // Create product
        Product::create($this->prepareProductData($request, $image));

        // Redirect to products index
        return redirect()->route('apps.products.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        // Get categories for editing product
        $categories = Category::all();

        // Return inertia view with product and categories
        return Inertia::render('Apps/Products/Edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // Validate the product data
        $this->validateProduct($request, $product);

        // Check if a new image is uploaded
        $image = $request->file('image') ? $this->storeImage($request, $product) : $product->image;

        // Update the product data
        $product->update($this->prepareProductData($request, $image));

        // Redirect to products index
        return redirect()->route('apps.products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Delete image from storage
        Storage::disk('local')->delete('public/products/' . basename($product->image));

        // Delete the product
        $product->delete();

        // Redirect to products index
        return redirect()->route('apps.products.index');
    }

    /**
     * Validate the product data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product|null $product
     * @return void
     */
    private function validateProduct(Request $request, Product $product = null)
    {
        $rules = [
            'image'         => 'nullable|image|mimes:jpeg,jpg,png|max:2000',
            'barcode'       => 'required|unique:products,barcode,' . ($product ? $product->id : 'NULL'),
            'title'         => 'required',
            'description'   => 'required',
            'category_id'   => 'required',
            'buy_price'     => 'required',
            'sell_price'    => 'required',
            'stock'         => 'required',
        ];

        $this->validate($request, $rules);
    }

    /**
     * Store the product image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product|null $product
     * @return string
     */
    private function storeImage(Request $request, Product $product = null)
    {
        if ($product && $product->image) {
            // Remove old image
            Storage::disk('local')->delete('public/products/' . basename($product->image));
        }

        // Store new image
        $image = $request->file('image');
        return $image->storeAs('public/products', $image->hashName());
    }

    /**
     * Prepare product data for storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $image
     * @return array
     */
    private function prepareProductData(Request $request, $image)
    {
        return [
            'image'         => basename($image),
            'barcode'       => $request->barcode,
            'title'         => $request->title,
            'description'   => $request->description,
            'category_id'   => $request->category_id,
            'buy_price'     => $request->buy_price,
            'sell_price'    => $request->sell_price,
            'stock'         => $request->stock,
        ];
    }
}
