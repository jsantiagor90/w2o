<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Enterprises;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $employeeQuery = Employees::query()->where('enterprise_id', $request->enterprise_id)->get();
        return response()->json($employeeQuery);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enterprise_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'birth_date'=> '',
        ]);

        if($validator->fails()) {
            return response()->json(['error' => true, 'details' => $validator->messages()], 500);
        }

        $data = [
            'enterprise_id' => $request->input('enterprise_id') ?: null,
            'name' => $request->input('name') ?: null,
            'phone' => $request->input('phone') ?: null,
            'email' => $request->input('email') ?: null,
            'birth_date' => $request->input('birth_date') ? self::formatDateToSave($request->input('birth_date')) : null
        ];

        $employee = Employees::create($data);

        return response()->json($employee);
    }

    /**
     * @param Employees $employee
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Employees $employee)
    {
        return response()->json($employee);
    }

    /**
     * @param Request $request
     * @param Employees $employee
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Employees $employee)
    {
        $validator = Validator::make($request->all(), [
            'enterprise_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'birth_date'=> '',
        ]);

        if($validator->fails()) {
            return response()->json(['error' => true, 'details' => $validator->messages()], 500);
        }

        $data = [
            'enterprise_id' => $request->input('enterprise_id') ?: null,
            'name' => $request->input('name') ?: null,
            'phone' => $request->input('phone') ?: null,
            'email' => $request->input('email') ?: null,
            'birth_date' => $request->input('birth_date') ? self::formatDateToSave($request->input('birth_date')) : null
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

    public static function formatDateToSave($date)
    {
        if (str_contains($date, '/')) {
            $explodedDate = explode('/', $date);
            return "$explodedDate[2]-$explodedDate[1]-$explodedDate[0]";
        }

        return $date;
    }
}
