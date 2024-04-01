<?php

namespace App\Http\Controllers\API;

use App\Helper\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function fetch(Request $request) {
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $age = $request->input('age');
        $phone = $request->input('phone');
        $unit_id = $request->input('unit_id');
        $role_id = $request->input('role_id');
        $limit = $request->input('limit', 10);

        $employeeQuery = Employee::query();

        if($id) {
            $employee = $employeeQuery->find($id);

            if($employee) {
                return ResponseFormatter::success([
                    'status' => true,
                    'msg' => 'Employee Found',
                    'data' => $employee
                ]);
            }

            return ResponseFormatter::error([
                'status' => false,
                'msg' => 'Employee not found'
            ], 404);
        }

        $employees = $employeeQuery;

        if($name) {
            $employees->where('name', 'like', '%'.$name.'%');
        }

        if($email) {
            $employees->where('email', 'like', '%'.$email.'%');
        }

        if($age) {
            $employees->where('age', $age);
        }

        if($phone) {
            $employees->where('phone', 'like', '%'.$phone.'%');
        }

        if($unit_id) {
            $employees->where('unit_id', $unit_id);
        }

        if($role_id) {
            $employees->where('role_id', $role_id);
        }

        return ResponseFormatter::success([
            'status' => true,
            'msg' => 'Employees Found',
            'data' => $employees->paginate($limit)
        ]);
    }

    public function employeeHistory(Request $request) {
        $search = [
            "employee_id" => $request->input('employee_id') !== null ? $request->input('employee_id') : auth()->user()->employee_id,
            "start_date" => $request->input('start_date') !== '' ? $request->input('start_date') : date('Y-m-d'),
            "end_date" => $request->input('end_date') !== '' ? $request->input('end_date') : date('Y-m-d'),
        ];

        $result = null;
        $model = new Employee();

        $result = $model->employeeHistory($search);

        return ResponseFormatter::success([
            'status' => true,
            'msg' => 'History Found',
            'data' => $result
        ]);
    }

    public function create(CreateEmployeeRequest $request) {
        try {
            if($request->hasFile('photo')){
                $path = $request->file('photo')->store('public/photos/employees'); // Simpan file di dalam direktori storage/app/files/cuti
                $path = str_replace('public/photos/employees', 'storage/photos/employees', $path); // Ubah path agar sesuai dengan penyimpanan publik
            }

            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : '',
                'unit_id' => $request->unit_id,
                'role_id' => $request->role_id,
            ]);

            if(!$employee) {
                throw new Exception('Employee not created');
            }

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Employee Created',
                'data' => $employee
            ]);
        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateEmployeeRequest $request, $id) {
        try {
            $employee = Employee::find($id);

            if(!$employee) {
                throw new Exception('Employee not found');
            }

            if($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos/employees');
            }

            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : '',
                'unit_id' => $request->unit_id,
                'role_id' => $request->role_id,
            ]);

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Employee Updated Successfully',
                'data' => $employee
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy($id) {
        try {
            $employee = Employee::find($id);

            if(!$employee) {
                throw new Exception('Employee not found');
            }

            $employee->delete();

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'Employee Deleted Successfully'
            ]);
        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

    public function getEmployee(Request $request) {
        try {
            $search = $request->input('search');

            $employee = Employee::query();

            $employee = Employee::select('employees.name', 'divisi.name as name_divisi', 'jabatan.name as name_jabatan')
                        ->from('employees')
                        ->join('jabatan', 'jabatan.id', '=', 'employees.jabatan_id')
                        ->join('divisi', 'divisi.id', '=', 'employees.unit_id')
                        ->where('employees.is_verified', '1')
                        ->orderBy('employees.name');

            if($search != null) {
                $employee->where(function ($query) use ($search) {
                    $query->where('employees.name', 'like', '%' . $search . '%')
                        ->orWhere('divisi.name', 'like', '%' . $search . '%')
                        ->orWhere('jabatan.name', 'like', '%' . $search . '%');
                });
            }

            return ResponseFormatter::success([
                'status' => true,
                'msg' => 'List Pegawai Found',
                'data' => $employee->get()
            ]);

        } catch (Exception $th) {
            return ResponseFormatter::error([
                'status' => false,
                'msg' => $th->getMessage(),
            ], 500);
        }
    }

}
