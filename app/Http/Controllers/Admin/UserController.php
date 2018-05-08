<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
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
        $user = new User($request->all());
        $user->password = bcrypt($request->password);
        $user->logo = Auth::user()->logo;
        $user->business_name = Auth::user()->business_name;
        $user->email = Auth::user()->email;
        $user->address = Auth::user()->address;
        $user->phone = Auth::user()->phone;
        $user->level = intval($request->level);
        $user->generatePublicId();
        $user->save();

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
}
