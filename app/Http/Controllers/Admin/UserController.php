<?php

namespace App\Http\Controllers\Admin;

use App\Product;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
     * construct
     */
    public function __construct()
    {
        $this->middleware('admin')->except([
            'search',
            'assistantList'
        ]);

       // $this->middleware('doctor')->only(['assistantList']);

        $this->middleware('secretary')->only(['search']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! empty($request->search)) {

            $users = User::where('id', '<>', Auth::user()->id)
                ->orderByDesc('id')
                ->where('username', 'like', "%$request->search%")
                ->orWhere('name', 'like', "%$request->search%")
                ->orWhere('public_id', 'like', "%$request->search%")
                ->paginate();

        } else {
            $users = User::where('id', '<>', Auth::user()->id)->orderByDesc('id')->paginate();
        }

        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $user = new User($request->all());
        $user->password = bcrypt($request->password);
        $user->logo = Auth::user()->logo;
        $user->business_name = Auth::user()->business_name;
        $user->email = Auth::user()->email;
        $user->address = Auth::user()->address;
        $user->phone = Auth::user()->phone;
        $user->level = intval($request->level);
        $user->generatePublicId();
        $user->external = $request->level == User::LEVEL_DOCTOR ? $request->external : false;
        $user->save();

        $products = Product::all();

        foreach ($products as $product) {

            $user->commissionProducts()->attach($product->id, [
                'commission' => User::DEFAULT_PRODUCT_COMMISSION
            ]);
        }

        DB::commit();

        $this->sessionMessage('message.user.create');

        return new JsonResponse(['success' => true, 'redirect' => route('user.index')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::where('public_id', $id)->first();

        if (! $user) {
            abort(404);
        }

        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::where('public_id', $id)->firstOrFail();

        $user->name = $request->name;
        $user->level = $request->level;
        $user->external = $request->level == User::LEVEL_DOCTOR ? $request->external : false;
        $user->save();

        $this->sessionMessage('message.user.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('user.edit', ['user' => $id])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('public_id', $id)->firstOrFail();
        $user->delete();

        $this->sessionMessage('message.user.delete');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('user.index')
        ]);
    }

    /**
     * Actualiza la contraseña del usuario
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function changePassword(Request $request, $id)
    {
        $user = User::where('public_id', $id)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->save();

        $this->sessionMessage('message.user.password');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('user.edit', ['user' => $id])
        ]);
    }

    /**
     * Verifica si un username esta disponible
     *
     * @param $username
     * @return JsonResponse
     */
    public function verifyUsername($username)
    {
        $user = User::where('username', $username)->first();

        if ($user) {
            return new JsonResponse(['success' => true, 'valid' => false]);
        }

        return new JsonResponse(['success' => true, 'valid' => true]);
    }

    /**
     * Obtiene una lista de usuarios aplicando los
     * filtros
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        $users = User::orderBy('id', 'DESC')
            ->orderBy('name')
            ->with('commissionProducts')
            ->limit(10);

        if (! empty($request->level)) {
            $users->where([
                ['level', '=', $request->level]
            ]);
        }

        if (! empty($request->search)) {
            $users
                ->where([
                    ['name', 'LIKE', "%$request->search%", 'or'],
                    ['email', 'LIKE', "%$request->search%", 'or'],
                    ['phone', 'LIKE', "%$request->search%", 'or']
                ])
            ;
        }

        return new JsonResponse([
            'success' => true,
            'users' => $users->get()
        ]);
    }

    /**
     * Obtiene todos los asistentes
     *
     * @return JsonResponse
     */
    public function assistantList()
    {
        $assistants = User::orderBy('name')->where('level', User::LEVEL_ASSISTANT)->get();

        return new JsonResponse([
            'success' => true,
            'assistants' => $assistants
        ]);
    }
}
