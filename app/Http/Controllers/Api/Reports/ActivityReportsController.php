<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\ActivitiesResource;
use App\Http\Resources\User\ActivityTypeResource;
use App\Observers\User\LogActivityObserver;
use Illuminate\Http\Request;

class ActivityReportsController extends Controller
{

    protected $logActivityObserver;

    public function __construct(LogActivityObserver $logActivityObserver)
    {
        $this->logActivityObserver      = $logActivityObserver;
    }

    public function activity(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->logActivityObserver->getData($request);
        $types  = $this->logActivityObserver->getWithoutFilter()->groupBy('log_name')->get(['log_name']);

        $totalRows      = $data->count();
        $activities     = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'types'         => ActivityTypeResource::collection($types),
            'activities'    => ActivitiesResource::collection($activities),
        ], 200);
    }
}
