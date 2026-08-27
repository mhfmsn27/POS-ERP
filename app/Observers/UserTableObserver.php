<?php

namespace App\Observers;

use App\Models\UserTableView;
use Illuminate\Http\Request;

class UserTableObserver
{
    public function getData(String $tableName)
    {
        return UserTableView::where('user_id', auth()->user()->id)->where('table', $tableName)->first(['view_option', 'id']);
    }

    public function createUpdate(Request $request)
    {
 
        if ($this->getData($request->table) == null) {
            return UserTableView::create([
                'user_id'           => auth()->user()->id,
                'table'             => $request->table,
                'view_option'       => implode(",",$request->options)
            ]);
        } else {
            $this->getData($request->table)->update([
                'view_option'       => implode(",",$request->options)
            ]);
        }
    }
}
