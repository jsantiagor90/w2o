<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Enterprises;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
//        $employeQuery = Employees::all();
        $employeQuery = Employees::query()->where('enterprise_id', $request->enterprise_id)->get();
//        dd($employeQuery);
        return response()->json($employeQuery);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = [
            'enterprise_id' => $request->input('enterprise_id') ?: null,
            'name' => $request->input('name') ?: null,
            'phone' => $request->input('phone') ?: null,
            'email' => $request->input('email') ?: null,
            'birth_date' => $request->input('birth_date') ?: null
        ];

        $enterprise = Employees::create($data);

        return response()->json($enterprise);
    }

    /**
     * @param Request $request
     * @param Employees $employee
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Employees $employee)
    {
        $data = [
            'enterprise_id' => $request->input('enterprise_id') ?: null,
            'name' => $request->input('name') ?: null,
            'phone' => $request->input('phone') ?: null,
            'email' => $request->input('email') ?: null,
            'birth_date' => $request->input('birth_date') ?: null
        ];
        $employee->update($data);

        return response()->json($employee);
    }

    /**
     * @param $employee
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Employees $employee)
    {
        $employee->delete();

        return response()->json();
    }
}
