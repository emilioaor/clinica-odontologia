<?php

namespace App\Http\Controllers\User;

use App\EmailAttach;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmailController extends Controller
{
    /**
     *
     */
    public function __construct()
    {
       $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('emailAttaches')->get();

        return view('user.email.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(404);
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
        abort(404);
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
        DB::beginTransaction();

        $product = Product::where('public_id', $id)->firstOrFail();
        $product->email_title = $request->email_title;
        $product->email_text = $request->email_text;
        $product->send_email = $request->send_email;
        $product->save();

        $attachIds = [];

        foreach ($request->email_attaches as $attach) {

            if (isset($attach['file']) && isset($attach['filename'])) {
                // Si tiene estos parametros es porque es un documento que recien se esta cargando

                $base64 = explode(',', $attach['file']);

                $upload = base64_decode($base64[1]);
                $explode = explode(';', $attach['file']);
                $explode = explode('/', $explode[0]);
                $extension = $explode[1];

                $filename = uniqid() . '.' . $extension;
                $url = 'uploads/email_attach/' . $product->id . '/' . $filename;
                $path = public_path('uploads/email_attach') . '/' . $product->id;

                if (! is_dir($path)) {
                    mkdir($path);
                }

                $path .= '/' . $filename;

                file_put_contents($path, $upload);

                $emailAttach = new EmailAttach();
                $emailAttach->product_id = $product->id;
                $emailAttach->filename = $attach['filename'];
                $emailAttach->url = $url;
                $emailAttach->save();

                // Guardo el id de todos los documentos adjuntos
                $attachIds[] = $emailAttach->id;

            } else {
                // Guardo el id de todos los documentos adjuntos
                $attachIds[] = $attach['id'];
            }
        }

        // Elimina todos los adjuntos que no llegaron en la peticion
        EmailAttach::query()->where('product_id', $product->id)->whereNotIn('id', $attachIds)->delete();

        DB::commit();

        $this->sessionMessage('message.email.update');

        return new JsonResponse(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort(404);
    }
}
