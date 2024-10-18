<?php

namespace App\Scheduler;

use App\Service\FlightPriceService;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('0 0 * * *')]
final class UpdatePrices
{
    private FlightPriceService $flightPriceService;

    public function __construct(FlightPriceService $flightPriceService)
    {
        $this->flightPriceService = $flightPriceService;
    }

    public function __invoke()
    {
        // Call the service that handles flight price updates
        $this->flightPriceService->updateFlightPrices();
    }
}
