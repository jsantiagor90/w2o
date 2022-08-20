<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnterpriseRequest;
use App\Models\Enterprises;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnterpriseController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $enterpriseQuery = Enterprises::all();

        return response()->json($enterpriseQuery);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'document' => 'required|unique:enterprises',
            'phone' => 'required',
            'email' => '',
            'zipcode'=> '',
            'state'=> '',
            'city'=> '',
            'neighborhood'=> '',
            'street'=> '',
            'number'=> '',
            'complement'=> '',
        ]);

        if($validator->fails()) {
            return response()->json(['error' => true, 'details' => $validator->messages()], 500);
        }

        $data = [
            'name' => $request->input('name') ?: null,
            'document' => $request->input('document') ?: null,
            'phone' => $request->input('phone') ?: null,
            'email' => $request->input('email') ?: null,
            'zipcode'=> $request->input('zipcode') ?: null,
            'state'=> $request->input('state') ?: null,
            'city'=> $request->input('city') ?: null,
            'neighborhood'=> $request->input('neighborhood') ?: null,
            'street'=> $request->input('street') ?: null,
            'number'=> $request->input('number') ?: null,
            'complement'=> $request->input('complement') ?: null
        ];
        $enterprise = Enterprises::create($data);

        return response()->json($enterprise);
    }

    /**
     * @param Enterprises $enterpriseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Enterprises $enterprise)
    {

//        $enterprise = Enterprises::find($enterpriseId);

        return response()->json($enterprise);
    }
    /**
     * @param Request $request
     * @param Enterprises $enterprise
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,Enterprises $enterprise)
    {
        $data = [
            'name' => $request->input('name') ?: null,
            'document' => $request->input('document') ?: null,
            'phone' => $request->input('phone') ?: null,
            'email' => $request->input('email') ?: null,
            'zipcode'=> $request->input('zipcode') ?: null,
            'state'=> $request->input('state') ?: null,
            'city'=> $request->input('city') ?: null,
            'neighborhood'=> $request->input('neighborhood') ?: null,
            'street'=> $request->input('street') ?: null,
            'number'=> $request->input('number') ?: null,
            'complement'=> $request->input('complement') ?: null
        ];
        $enterprise->update($data);

        return response()->json($enterprise);
    }

    /**
     * @param Enterprises $enterprise
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Enterprises $enterprise)
    {
        $enterprise->delete();

        return response()->json();
    }
}
