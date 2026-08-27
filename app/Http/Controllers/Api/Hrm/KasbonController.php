<?php

namespace App\Http\Controllers\Api\Hrm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hrm\KasbonRequest;
use App\Http\Resources\Hrm\KabsonDetailResource; 
use App\Models\Hrm\Employee;
use App\Models\Hrm\EmployeeKasbon;
use App\Observers\Hrm\KasbonObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class KasbonController extends Controller
{
    protected $kasbonObserver;

    public function __construct(KasbonObserver $kasbonObserver)
    {
        $this->kasbonObserver   = $kasbonObserver;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('kasbon_view'), 403);
        $limit  = $request->input('limit', 10);
        $data   = $this->kasbonObserver->getData($request);

        $totalRows  = $data->count();
        $kasbons    = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'kasbons'       => KabsonDetailResource::collection($kasbons),
        ]);
    }

    public function detail(EmployeeKasbon $kasbon)
    {
        return response()->json([ 
            'detail'       => KabsonDetailResource::collection($kasbon),
        ]);
    }

    public function store(KasbonRequest $request)
    {
        abort_if(Gate::denies('kasbon_create'), 403);
        try {

            DB::beginTransaction();
            $employee   = Employee::find($request->employee['id']);
            $this->kasbonObserver->createData($request, $employee);

            DB::commit();

            return response()->json([
                'message'   => 'Data kasbon di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function update(KasbonRequest $request, EmployeeKasbon $kasbon)
    {
        abort_if(Gate::denies('kasbon_update'), 403);
        try {

            DB::beginTransaction();

            $employee   = Employee::find($request->employee['id']);
            $this->kasbonObserver->updateData($request, $kasbon, $employee);

            DB::commit();

            return response()->json([
                'message'   => 'Data kasbon di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 422);
        }
    }

    public function delete(EmployeeKasbon $kasbon)
    {
        abort_if(Gate::denies('kasbon_delete'), 403);
        try {

            DB::beginTransaction();
            $this->kasbonObserver->deleteData($kasbon);
            DB::commit();

            return response()->json([
                'message'   => 'Data kasbon di hapus',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }
}
