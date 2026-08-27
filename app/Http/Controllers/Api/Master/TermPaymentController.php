<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\TermPaymentRequest;
use App\Http\Resources\Master\TermPaymentResource;
use App\Models\Admin\TermPayment;
use App\Observers\Master\TermPaymentObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TermPaymentController extends Controller
{
    protected $termPaymentObserver;

    public function __construct(TermPaymentObserver $termPaymentObserver)
    {
        $this->termPaymentObserver      = $termPaymentObserver;
    }


    public function index(Request $request)
    {
 
        abort_if(Gate::denies('payment_term_view'), 403);

        $limit  = $request->input('limit', 20);
        $data   = $this->termPaymentObserver->getData($request);

        $totalRows  = $data->count();
        $terms      = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'terms'         => TermPaymentResource::collection($terms),
        ]);
    }


    public function create(TermPaymentRequest $request)
    {
        abort_if(Gate::denies('payment_term_create'), 403);

        try {

            $this->termPaymentObserver->createData($request);

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

    public function update(TermPaymentRequest $request, TermPayment $term)
    {

        abort_if(Gate::denies('payment_term_update'), 403);

        try {

            $this->termPaymentObserver->updateData($request, $term);

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

    public function setDefault(TermPayment $term)
    {

        abort_if(Gate::denies('payment_term_update'), 403);

        try {
            if ($term->default == 'yes') {
                $term->update([
                    'default'       => 'yes'
                ]);
            } else {
                TermPayment::where("default", "yes")->update([
                    'default'       => 'no'
                ]);

                $term->update([
                    'default'       => 'yes'
                ]);
            }

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

    public function delete(TermPayment $term)
    {

        abort_if(Gate::denies('payment_term_delete'), 403);

        try {

            $term->delete();

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
