import 'maplibre-gl/dist/maplibre-gl.css';

import { useEffect, useRef, useState } from 'react';
import Map, { Marker, NavigationControl } from 'react-map-gl/maplibre';
import type { MapRef } from 'react-map-gl/maplibre';

import carIcon from '../assets/svg/car.svg';
import { mapConfig } from '../config/map';
import type { CarMapProps } from '../types/map';
import CarHistoryModal from './CarHistoryModal';

export default function CarMap({
    cars,
    zoom = mapConfig.defaultZoom,
}: CarMapProps) {
    const mapRef = useRef<MapRef>(null);
    const [selectedCarIdent, setSelectedCarIdent] = useState<string | null>(
        null,
    );
    const initialCar = cars[0];
    const initialPosition = initialCar
        ? {
              latitude: initialCar.position_latitude,
              longitude: initialCar.position_longitude,
          }
        : mapConfig.defaultPosition;

    useEffect(() => {
        if (cars.length === 0) {
            return;
        }

        if (cars.length === 1) {
            mapRef.current?.flyTo({
                center: [cars[0].position_longitude, cars[0].position_latitude],
                zoom,
                duration: mapConfig.transitionDuration,
            });

            return;
        }

        const longitudes = cars.map((car) => car.position_longitude);
        const latitudes = cars.map((car) => car.position_latitude);

        mapRef.current?.fitBounds(
            [
                [Math.min(...longitudes), Math.min(...latitudes)],
                [Math.max(...longitudes), Math.max(...latitudes)],
            ],
            {
                padding: mapConfig.boundsPadding,
                maxZoom: mapConfig.maxBoundsZoom,
                duration: mapConfig.transitionDuration,
            },
        );
    }, [cars, zoom]);

    return (
        <>
            <div className="h-[70vh] min-h-96 w-full overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                <Map
                    ref={mapRef}
                    initialViewState={{ ...initialPosition, zoom }}
                    mapStyle={mapConfig.styleUrl}
                    workerUrl={mapConfig.workerUrl}
                    style={{ width: '100%', height: '100%' }}
                >
                    <NavigationControl position="top-right" />

                    {cars.map((car) => (
                        <Marker
                            key={car.ident}
                            latitude={car.position_latitude}
                            longitude={car.position_longitude}
                            anchor="center"
                        >
                            <button
                                type="button"
                                className="cursor-pointer"
                                aria-label={`Show history for vehicle ${car.ident}`}
                                onClick={() => setSelectedCarIdent(car.ident)}
                            >
                                <img
                                    src={carIcon}
                                    alt=""
                                    className="h-14 w-14 drop-shadow-lg"
                                />
                            </button>
                        </Marker>
                    ))}
                </Map>
            </div>

            {selectedCarIdent && (
                <CarHistoryModal
                    ident={selectedCarIdent}
                    onClose={() => setSelectedCarIdent(null)}
                />
            )}
        </>
    );
}
