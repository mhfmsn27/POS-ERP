<?php

namespace App\Imports\Master;

use App\Helper;
use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Transaction\RekonsiliasiData;
use App\Models\Transaction\SmartlinkBank;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;

class RekonsiliasiImport implements ToCollection, WithStartRow
{
    protected $total_rows = 0;
    protected $errors = [];
    protected $bank_account = null;
    protected $account;

    public function __construct(Account $account)
    {
        $this->account  = $account;
    }

    public function startRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        $accountNumber = Helper::fresh_aprice($rows[0][2]) ?? null;
        $endBalance    = 0;

        if (!$accountNumber) {
            $this->errors[] = "Account number not found in Excel file";
            return null;
        }

        $this->bank_account = SmartlinkBank::where('rekening', $accountNumber)->where('account_id', $this->account->id)->first();

        if (!$this->bank_account) {
            $this->errors[] = "Bank account {$accountNumber} not found in system";
            return null;
        }

        foreach ($rows->slice(5) as $row) {

            if (isset($row[0]) && stripos($row[0], 'Saldo Akhir') !== false) {
                $endBalance = (float)$row[2];
            }

            try {
                if (!$row[0] || !$row[1]) continue;

                $date               = substr($row[0], 0, 1) == "'" ? substr($row[0], 0, 1) : $row[0];

                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', trim($date))) {
                    $date               = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');

                    $rekonsiliasiBank   = RekonsiliasiData::whereDate('date', $date)->where('amount', (float)$row[3])->where('note', $row[1])->first(['id']);

                    if (!$rekonsiliasiBank) {
                        // $dateRange = [
                        //     Carbon::parse($date)->subDays(3)->format('Y-m-d'),
                        //     Carbon::parse($date)->addDays(2)->format('Y-m-d')
                        // ];

                        // $accountTransaction             = AccountTransaction::where('after_rekonsiliasi', 'no')->where('account_id', $this->account->id)->where('amount', (float)$row[3])->whereBetween('date', $dateRange)->first(['id']);

                        RekonsiliasiData::create([
                            'amount'                    => (float)$row[3],
                            'saldo'                     => (float)$row[5],
                            'account_id'                => $this->account->id,
                            'date'                      => $date,
                            'note'                      => $row[1],
                            'type'                      => $row[4] == 'CR' ? 'debit' : 'credit'
                        ]);

                        $this->total_rows++;
                    }
                } else {
                }
            } catch (\Exception $e) {
                $this->errors[] = "Error on row {$this->total_rows}: " . $e->getMessage();
            }
        }

        if($this->bank_account)
        {
            if($this->bank_account->account) {
                $this->bank_account->account->update([
                    'end_balance'       => $endBalance
                ]);
            }
        } 

    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getTotalRows()
    {
        return $this->total_rows;
    }
}
