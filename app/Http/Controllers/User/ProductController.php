<?php

namespace App\Http\Controllers\User;

use App\Product;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->middleware('doctor')->except('index');
        $this->middleware('noAssistant');
        $this->middleware('admin')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderByDesc('created_at')->paginate();

        return view('user.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        $product = new Product($request->all());
        $product->public_id = 'PROD' . time();
        $product->save();

        $users = User::all();

        foreach ($users as $user) {

            $product->commissionUsers()->attach($user->id, [
                'commission' => User::DEFAULT_PRODUCT_COMMISSION
            ]);
        }

        DB::commit();

        $this->sessionMessage('message.product.create');

        return new JsonResponse(['success' => true, 'redirect' => route('product.index')]);
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
        $product = Product::where('public_id', $id)->first();

        if (! $product || ($product && ! $product->canEdit())) {
            abort(404);
        }

        return view('user.product.edit', compact('product'));
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
        $product = Product::where('public_id', $id)->firstOrFail();
        $product->public_id = $request->public_id;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->required_lab = $request->required_lab;
        $product->save();

        $this->sessionMessage('message.product.update');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('product.edit', ['product' => $product->public_id])
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
        $product = Product::where('public_id', $id)->firstOrFail();
        $product->delete();

        $this->sessionMessage('message.product.delete');

        return new JsonResponse([
            'success' => true,
            'redirect' => route('product.index')
        ]);
    }

    /**
     * Valida si el id esta disponible
     *
     * @param $publicId
     * @param Request $id
     * @return JsonResponse
     */
    public function validatePublicId($publicId, $id)
    {
        if (Product::where('public_id', $publicId)->where('id', '<>', $id)->count()) {
            return new JsonResponse(['success' => true, 'valid' => false]);
        }

        return new JsonResponse(['success' => true, 'valid' => true]);
    }
}
