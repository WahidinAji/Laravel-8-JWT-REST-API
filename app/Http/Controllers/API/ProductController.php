<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return \response()->json([
            'status' => true,
            'data' => $this->user->products()->get()
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'sku', 'price', 'quantity');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'sku' => 'required',
            'price' => 'required',
            'quantity' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->getMessageBag()], Response::HTTP_OK);
        }

        //Request is valid, create new product
        $product = $this->user->products()->create([
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'quantity' => $request->quantity
        ]);

        //Product created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], Response::HTTP_CREATED, ['Content-Type' => 'application/json;charset=UTF-8']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = $this->user->products()->find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product not found.'
            ], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        return \response()->json([
            'success' => true,
            'message' => "You got the product's",
            'data' => [
                'products' => $product
            ]
        ], Response::HTTP_OK, ['Content-Type' => 'application/json;charset=UTF-8']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //Validate data
        $data = $request->only('name', 'sku', 'price', 'quantity');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'sku' => 'required',
            'price' => 'required',
            'quantity' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->getMessageBag()], Response::HTTP_OK);
        }
        //Request is valid, update product
        $product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'quantity' => $request->quantity
        ]);
        // $data = [
        //     'name' => $product->name,
        //     'sku' => $product->sku,
        //     'price' => $product->price,
        //     'quantity' => $product->quantity
        // ];
        //Product updated, return success response
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ], Response::HTTP_OK, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if (!isset($product)) {
            return \response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND, ['Content-Type' => 'application/json; charset=utf-8']);
        }
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], Response::HTTP_OK, ['Content-Type' => 'application/json; charset=utf-8']);
    }
}
