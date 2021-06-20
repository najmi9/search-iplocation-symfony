<?php

declare(strict_types=1);

namespace App\Controller;

use App\Infrastructure\GeoLocation\IP2GeoLocation;
use App\Infrastructure\GeoLocation\IpGeoLocation;
use App\Infrastructure\GeoLocation\IpinfoLocation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    /**
     * @Route("/location", name="location")
     */
    public function index(Request $request, IP2GeoLocation $ip2Location, IpinfoLocation $ipinfoLocation, IpGeoLocation $ipLocation): Response
    {
        $ip = $request->getClientIp();

        $ip2Location = $ip2Location->location($ip);
        $ipinfoLocation = $ipinfoLocation->location($ip);
        $ipLocation = $ipLocation->location($ip);

        return $this->render('location/index.html.twig', [
            'ip' => $ip,
            'ip2Location' => $ip2Location,
            'ipinfoLocation' => $ipinfoLocation,
            'ipLocation' => $ipLocation,
        ]);
    }
}
