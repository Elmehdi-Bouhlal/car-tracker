export type Car = {
    ident: string;
    battery_voltage: number;
    channel_id: number;
    device_id: number;
    device_name: string;
    device_type_id: number;
    engine_ignition_status: boolean;
    event_priority_enum: number;
    external_powersource_voltage: number;
    gnss_state_enum: number;
    gnss_status: boolean;
    gsm_cellid: number;
    gsm_lac: number;
    gsm_mcc: number;
    gsm_mnc: number;
    gsm_operator_code: string;
    gsm_signal_level: number;
    movement_status: boolean;
    peer: string;
    position_altitude: number;
    position_direction: number;
    position_hdop: number;
    position_latitude: number;
    position_longitude: number;
    position_satellites: number;
    position_speed: number;
    position_valid: boolean;
    protocol_id: number;
    server_timestamp: number;
    timestamp: number;
    vehicle_mileage: number;
    created_at: string;
    updated_at: string;
};

export type CreateCarPayload = {
    'battery.voltage': number;
    'channel.id': number;
    'device.id': number;
    'device.name': string;
    'device.type.id': number;
    'engine.ignition.status': boolean;
    'event.priority.enum': number;
    'external.powersource.voltage': number;
    'gnss.state.enum': number;
    'gnss.status': boolean;
    'gsm.cellid': number;
    'gsm.lac': number;
    'gsm.mcc': number;
    'gsm.mnc': number;
    'gsm.operator.code': string;
    'gsm.signal.level': number;
    ident: string;
    'movement.status': boolean;
    peer: string;
    'position.altitude': number;
    'position.direction': number;
    'position.hdop': number;
    'position.latitude': number;
    'position.longitude': number;
    'position.satellites': number;
    'position.speed': number;
    'position.valid': boolean;
    'protocol.id': number;
    'server.timestamp': number;
    timestamp: number;
    'vehicle.mileage': number;
};

export type UpdateCarPayload = Partial<Omit<CreateCarPayload, 'ident'>>;

export type CarOperation = () => Promise<Car>;

export type CarPositionAction = 'created' | 'retrieved' | 'updated';

export type CarPositionEventPayload = {
    action: CarPositionAction;
    car: Car;
};

export type CarLog = {
    id: number;
    car_ident: string;
    changes: Partial<Car>;
    snapshot: Car;
    created_at: string;
};

export type CarHistoryModalProps = {
    ident: string;
    onClose: () => void;
};
