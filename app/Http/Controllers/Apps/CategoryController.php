namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreCategoryRequest; // Use a FormRequest class

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::when(request()->q, function($categories) {
            $categories = $categories->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);

        return Inertia::render('Apps/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Apps/Categories/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $imagePath = $this->uploadImage($request->file('image')); // Handle image upload
        
        Category::create([
            'image'         => $imagePath,
            'name'          => $request->name,
            'description'   => $request->description
        ]);

        return redirect()->route('apps.categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return Inertia::render('Apps/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCategoryRequest $request, Category $category)
    {
        // Check if there's a new image, then upload and update the category
        if ($request->file('image')) {
            // Delete old image before uploading new one
            $this->deleteImage($category->image);
            
            $imagePath = $this->uploadImage($request->file('image'));
            $category->update([
                'image' => $imagePath,
                'name' => $request->name,
                'description' => $request->description,
            ]);
        } else {
            // Update without changing the image
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);
        }

        return redirect()->route('apps.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        // Delete image if exists
        $this->deleteImage($category->image);

        // Delete category
        $category->delete();

        return redirect()->route('apps.categories.index');
    }

    /**
     * Helper function to handle image upload.
     */
    private function uploadImage($image)
    {
        $image->storeAs('public/categories', $image->hashName());
        return $image->hashName();
    }

    /**
     * Helper function to delete image from storage.
     */
    private function deleteImage($imagePath)
    {
        $imagePath = 'public/categories/' . basename($imagePath);
        if (Storage::exists($imagePath)) {
            Storage::delete($imagePath);
        }
    }
}
