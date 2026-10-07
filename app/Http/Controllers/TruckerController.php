<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarHistoryRequest;
use App\Http\Requests\ShowTruckerRequest;
use App\Http\Requests\StoreTruckerRequest;
use App\Http\Requests\UpdateTruckerRequest;
use App\Services\TruckerService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class TruckerController extends Controller
{
    public function __construct(
        private readonly TruckerService $truckerService,
    ) {}

    /**
     * Display the vehicle map with the persisted cars.
     */
    public function index(): InertiaResponse
    {
        return Inertia::render('index', [
            'cars' => $this->truckerService->getAll(),
        ]);
    }

    /**
     * Store a vehicle telemetry record.
     */
    public function store(StoreTruckerRequest $request): JsonResponse
    {
        $car = $this->truckerService->create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $car,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a vehicle telemetry record.
     */
    public function show(ShowTruckerRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->truckerService->findByIdent(
                $request->string('ident')->toString(),
            ),
        ]);
    }

    /**
     * Display the paginated update history for a vehicle.
     */
    public function history(CarHistoryRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->truckerService->getHistory(
                $request->string('ident')->toString(),
                $request->integer('per_page', 15),
            ),
        ]);
    }

    /**
     * Update a vehicle telemetry record.
     */
    public function update(UpdateTruckerRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->truckerService->updateByIdent(
                $request->string('route_ident')->toString(),
                $request->safe()->except(['route_ident']),
            ),
        ]);
    }
}
