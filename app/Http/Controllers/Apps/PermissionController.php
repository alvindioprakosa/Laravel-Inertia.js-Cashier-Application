namespace App\Http\Controllers\Apps;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Get the query parameter for search and paginate value
        $searchQuery = $request->get('q');
        $perPage = $request->get('per_page', 5); // Default to 5 per page if not provided

        // Query permissions based on search query and paginate
        $permissions = Permission::when($searchQuery, function($query) use ($searchQuery) {
            return $query->where('name', 'like', '%'. $searchQuery . '%');
        })->latest()->paginate($perPage);

        // Return inertia view with permissions data
        return inertia('Apps/Permissions/Index', [
            'permissions' => $permissions
        ]);
    }
}
