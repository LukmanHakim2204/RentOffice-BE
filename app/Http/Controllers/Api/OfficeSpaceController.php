<?php

namespace App\Http\Controllers\Api;

use App\Models\OfficeSpace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OfficeSpaceResource;

class OfficeSpaceController extends Controller
{
    public function index()
    {
        $offices = OfficeSpace::with(['city'])->get();
        return OfficeSpaceResource::collection($offices);
    }

    public function show(OfficeSpace $office)
    {
        $office->load('city', 'photos', 'benefits');
        return new OfficeSpaceResource($office);
    }
}
