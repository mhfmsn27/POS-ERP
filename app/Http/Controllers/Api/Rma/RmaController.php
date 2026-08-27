<?php

namespace App\Http\Controllers\Api\Rma;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rma\RmaRecordRequest;
use App\Http\Requests\Rma\RmaRequest;
use App\Http\Resources\Rma\RmaDetailResource;
use App\Http\Resources\Rma\RmaListResource;
use App\Http\Resources\Rma\RmaRecordResource;
use App\Models\Rma\RmaDetail;
use App\Models\Rma\RmaRecord;
use App\Models\Rma\RmaTransaction;
use App\Observers\Notification\NotificationObserver;
use App\Observers\Rma\RmaDetailObserver;
use App\Observers\Rma\RmaObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RmaController extends Controller
{
    protected $rmaObserver;
    protected $rmaDetailObserver;
    protected $notificationObserver;

    public function __construct(RmaObserver $rmaObserver, RmaDetailObserver $rmaDetailObserver, NotificationObserver $notificationObserver)
    {
        $this->rmaObserver              = $rmaObserver;
        $this->rmaDetailObserver        = $rmaDetailObserver;
        $this->notificationObserver     = $notificationObserver;
    }

    public function index(Request $request)
    {

        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->rmaObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => RmaListResource::collection($transactions),
        ], 200);
    }

    public function detail(RmaTransaction $transaction)
    {
        $qrCodeSvg = QrCode::size(80)
            ->format('svg')
            ->generate(route('detail.rma', $transaction->ref_no));
        return response()->json([
            'qr'                => $qrCodeSvg,
            'transactions'      => RmaDetailResource::make($transaction),
            'records'           => RmaRecordResource::collection($transaction->records)
        ], 200);
    }

    public function store(RmaRequest $request)
    {

        try {

            DB::beginTransaction();

            $transaction    = $this->rmaObserver->createData($request);
            $this->rmaDetailObserver->createData($request, $transaction);

            $templates  = $this->notificationObserver->getTemplate('rma_template');

            if ($templates && $request->phone != null) {
                $message = str_replace(
                    ['{customer}', '{estimate}', '{tracking}'],
                    [$transaction->customer_name, substr($transaction->estimate_date, 0, 10), $transaction->ref_no],
                    $templates->message
                );

                $this->notificationObserver->sendMessage($message, $request->phone);
            }

            DB::commit();

            return response()->json([
                'message'       => "Transaksi berhasil di buat",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    public function update(RmaRequest $request, RmaTransaction $transaction)
    {

        try {

            if ($transaction->status == 'complete' || $transaction->status == 'taken') {
                return response()->json([
                    'message'       => "Transaksi sudah tidak dapat di edit, karena sudah selesai",
                    'status'        => false
                ], 422);
            }

            DB::beginTransaction();

            $this->rmaObserver->updateData($request, $transaction);
            $this->rmaDetailObserver->updateData($request, $transaction);

            DB::commit();

            return response()->json([
                'message'       => "Informasi Penjualan berhasil di perbaharui",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }

    public function deleteRecord(RmaRecord $record)
    {

        $record->delete();

        return response()->json([
            'message'   => "Record Berhasil di hapus",
            'status'    => true
        ], 200);
    }

    public function deleteItem(RmaDetail $detail)
    {
        $detail->record()->delete();
        $detail->delete();

        return response()->json([
            'message'   => "Item Berhasil di Hapus",
            'status'    => true
        ], 200);
    }

    public function delete(RmaTransaction $transaction)
    {

        try {

            DB::beginTransaction();

            $transaction->records()->delete();
            $transaction->details()->delete();
            $transaction->delete();

            DB::commit();

            return response()->json([
                'message'   => "Transaksi berhasil di hapus",
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine()
            ], 409);
        }
    }

    public function addRecords(RmaRecordRequest $request, RmaTransaction $transaction)
    {
        try {

            DB::beginTransaction();

            $this->rmaObserver->setRecord($request, $transaction);

            DB::commit();

            return response()->json([
                'message'       => "Record berhasil di perbaharui",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    public function updateDetails(RmaRecordRequest $request, RmaDetail $detail)
    {
        try {

            DB::beginTransaction();

            $record = $this->rmaDetailObserver->setRecord($request, $detail);

            DB::commit();

            $templates  = $this->notificationObserver->getTemplate('rma_process_template');

            if ($templates) {
                $message = str_replace(
                    ['{customer}', '{status}', '{note}', '{tracking}'],
                    [($detail->transaction->customer_name ?? ''), $record->status_name, ($record->note ?? $record->subject), ($detail->transaction->ref_no ?? '')],
                    $templates->message
                );

                $this->notificationObserver->sendMessage($message, ($detail->transaction->phone ?? '-'));
            }

            return response()->json([
                'message'       => "Record berhasil di perbaharui",
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'file'      => $e->getFile(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }


    public function print(RmaTransaction $transaction)
    {
        $qr = QrCode::size(80)->generate(route('detail.rma', $transaction->ref_no));
        return view('print.rma.detail', ['page'  => 'Print Invoice Rma - ' . $transaction->ref_no], compact('transaction', 'qr'));
    }
}
