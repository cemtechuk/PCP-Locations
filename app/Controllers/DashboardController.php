<?php

namespace App\Controllers;

use App\Models\ApiKeyModel;
use App\Models\ApiLogModel;
use App\Models\AuditLogModel;
use App\Models\CabinetModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $cabinets = new CabinetModel();
        $apiKeys  = new ApiKeyModel();
        $apiLogs  = new ApiLogModel();
        $audit    = new AuditLogModel();
        $users    = new UserModel();

        return view('dashboard/index', [
            'title'           => 'Dashboard — PCP Locations',
            'totalCount'      => $cabinets->getTotalCount(),

            // Stats
            'statCabinets'    => $cabinets->getTotalCount(),
            'statExchanges'   => $cabinets->getTotalExchanges(),
            'statRegions'     => $cabinets->getTotalRegions(),
            'statUsers'       => $users->countAll(),
            'statActiveKeys'  => $apiKeys->where('active', 1)->countAllResults(),
            'statApiToday'    => $apiLogs->getTotalToday(),
            'statApiWeek'     => $apiLogs->getTotalThisWeek(),

            // Panels
            'topExchanges'    => $cabinets->getTopExchanges(8),
            'recentActivity'  => $audit->getRecent(15),
            'apiKeyUsage'     => $apiLogs->getCountPerKey(),
        ]);
    }
}
