<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConfigController extends Controller
{

    /**
     * Carga la vista de configuracion
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function config()
    {
        return view('user.config');
    }

    /**
     * Actualiza la clave del usuario
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    Lang::trans('message.config.changePassword.current')
                ]
            ]);
        }

        if ($request->password !== $request->password_confirmation) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    Lang::trans('message.config.changePassword.confirmation')
                ]
            ]);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        $this->sessionMessage('message.config.changePassword.success');

        return new JsonResponse(['success' => true, 'redirect' => route('config')]);
    }
}
