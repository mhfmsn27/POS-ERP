<?php

namespace App\Observers\Rma;

use App\Models\Rma\RmaDetail;
use App\Models\Rma\RmaRecord;
use App\Models\Rma\RmaTransaction;
use Illuminate\Http\Request;

class RmaDetailObserver
{
    public function createData(Request $request, RmaTransaction $rmaTransaction)
    {
        foreach ($request->items as $item) {

            if ($item['name'] != null && $item['name'] != '') {
                RmaDetail::create([
                    'rma_transactions_id'           => $rmaTransaction->id,
                    'product_name'                  => $item['name'],
                    'complaint'                     => $item['complaint'],
                    'completeness'                  => $item['completeness'],
                ]);
            }
        }
    }

    public function updateData(Request $request, RmaTransaction $rmaTransaction)
    {
        foreach ($request->items as $item) {

            if ($item['id'] == null) {
                RmaDetail::create([
                    'rma_transactions_id'           => $rmaTransaction->id,
                    'product_name'                  => $item['name'],
                    'complaint'                     => $item['complaint'],
                    'completeness'                  => $item['completeness'],
                ]);
            } else {
                RmaDetail::where("id", $item['id'])->update([
                    'rma_transactions_id'           => $rmaTransaction->id,
                    'product_name'                  => $item['name'],
                    'complaint'                     => $item['complaint'],
                    'completeness'                  => $item['completeness'],
                ]);
            }
        }
    }

    public function setRecord(Request $request, RmaDetail $detail)
    {
        $record = RmaRecord::create([
            'rma_detail_id'         => $detail->id,
            'rma_transactions_id'   => $detail->rma_transactions_id,
            'subject'               => $request->subject,
            'type'                  => $request->type,
            'note'                  => $request->note,
        ]);

        if ($request->type != 'note') {

            $detail->update([
                'status'            => $request->type,
            ]);

            if ($detail->transaction->status == 'pending' && $request->type == 'process') {
                $detail->transaction->update([
                    'status'            => 'process'
                ]);
            }

            if ($request->type == 'complete') {

                if ($detail->transaction->complete_detail == $detail->transaction->details->count()) {
                    $detail->transaction->update([
                        'status'            => 'complete'
                    ]);
                }
            }

            if ($request->type == 'taken') {
                if ($detail->transaction->taken_detail == $detail->transaction->details->count()) {
                    $detail->transaction->update([
                        'status'            => 'taken'
                    ]);
                }
            }
        }

        return $record;
    }
}
