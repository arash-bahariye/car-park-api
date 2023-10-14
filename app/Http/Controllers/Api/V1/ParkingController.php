<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParkingResource;
use App\Models\Parking;
use App\Services\ParkingPriceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParkingController extends Controller
{
    public function start(Request $request){
        $parkingData = $request->validate([
            'vehicle_id' => ['required','integer','exists:vehicles,id,deleted_at,NULL,user_id,'.auth()->id()],
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
        ]);

        if(Parking::active()->where('vehicle_id', $request->vehicle_id)->exists()){
            return response()->json([
                'errors' => ['general' => ['Can not start parking twice using same vehicle']]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $parking = Parking::create($parkingData);
        $parking->load('vehicle', 'zone');

        return ParkingResource::make($parking);
    }

    public function show(Parking $parking){
        return ParkingResource::make($parking);
    }

    public function stop(Parking $parking){
        if ($parking->stop_time === Null){
            $parking->update([
                'stop_time' => now(),
                'total_price' => ParkingPriceService::calculatePrice($parking->zone_id, $parking->start_time, $parking->stop_time),
            ]);
            return ParkingResource::make($parking);
        } else {
            return response()->json([
                'errors' => ['general' => 'This parking has already been stoped']
            ], Response::HTTP_CONFLICT);
        }

    }
}
