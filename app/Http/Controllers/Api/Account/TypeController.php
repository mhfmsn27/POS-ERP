<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\TypeAccountRequest;
use App\Http\Requests\Account\TypeAccountUpdateRequest;
use App\Http\Resources\Account\TypeResource;
use App\Models\Account\AccountType;
use App\Observers\Account\TypeObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TypeController extends Controller
{
    protected $typeObserver;

    public function __construct(TypeObserver $typeObserver)
    {
        $this->typeObserver     = $typeObserver;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('account_type_view'), 403);

        $limit = $request->input('limit', 10);
        $data   = $this->typeObserver->getData($request);

        $totalRows  = $data->count();
        $types      = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'types'         => TypeResource::collection($types),
        ]);
    }


    public function create(TypeAccountRequest $request)
    {
        try {
            
            abort_if(Gate::denies('account_type_create'), 403);
            $this->typeObserver->createData($request);

            return response()->json([
                'message'   => 'Data berhasil di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function update(TypeAccountUpdateRequest $request, AccountType $type)
    {
        
        try {
 
            abort_if(Gate::denies('account_type_update'), 403);
            $this->typeObserver->updateData($request, $type);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function delete(AccountType $type)
    {

        abort_if(Gate::denies('account_type_delete'), 403);

        try {

            if ($type->edit_option == 'no') {
                return response()->json([
                    'message'   => 'Maaf, Data ini tidak bisa di hapus',
                    'status'    => true
                ], 422);
            }

            $type->delete();

            return response()->json([
                'message'   => 'Data berhasil di hapus',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }
}
