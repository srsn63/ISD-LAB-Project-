<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [];

        // Entrance & Check‑in
        for ($i=1; $i<=12; $i++) {
            $rows[] = [
                'type' => 'checkin', 'code' => sprintf('CI-%02d', $i), 'name' => "Check‑in Counter $i",
                'status' => $i <= 8 ? 'active' : 'closed', 'today_count' => $i <= 8 ? rand(40, 120) : 0,
            ];
        }
        for ($i=1; $i<=10; $i++) {
            $rows[] = [
                'type' => 'kiosk', 'code' => sprintf('K-%02d', $i), 'name' => "Self Kiosk $i",
                'status' => $i <= 7 ? 'active' : 'closed', 'today_count' => $i <= 7 ? rand(20, 60) : 0,
            ];
        }
        for ($i=1; $i<=6; $i++) {
            $rows[] = [
                'type' => 'baggage_drop', 'code' => sprintf('BD-%02d', $i), 'name' => "Baggage Drop $i",
                'status' => $i <= 5 ? 'active' : 'closed', 'today_count' => $i <= 5 ? rand(60, 140) : 0,
            ];
        }

        // Security & Immigration
        for ($i=1; $i<=8; $i++) {
            $rows[] = [
                'type' => 'security', 'code' => sprintf('SEC-%02d', $i), 'name' => "Security Lane $i",
                'status' => $i <= 6 ? 'open' : 'closed', 'today_count' => $i <= 6 ? rand(80, 180) : 0,
                'meta' => json_encode(['wait_min' => rand(5, 12)]),
            ];
        }
        for ($i=1; $i<=10; $i++) {
            $rows[] = [
                'type' => 'immigration_dep', 'code' => sprintf('IMD-%02d', $i), 'name' => "Departure Immigration $i",
                'status' => $i <= 7 ? 'open' : 'closed', 'today_count' => $i <= 7 ? rand(70, 150) : 0,
                'meta' => json_encode(['wait_min' => rand(4, 10)]),
            ];
        }
        for ($i=1; $i<=6; $i++) {
            $rows[] = [
                'type' => 'customs', 'code' => sprintf('CUS-%02d', $i), 'name' => "Customs $i",
                'status' => $i <= 4 ? 'open' : 'closed', 'today_count' => $i <= 4 ? rand(50, 120) : 0,
                'meta' => json_encode(['wait_min' => rand(3, 8)]),
            ];
        }

        // Departure Area
        for ($i=1; $i<=14; $i++) {
            $code = ($i <= 7 ? 'A' : 'B') . ($i <= 7 ? $i : $i-7);
            $status = ($i === 4 || $i === 9) ? 'boarding' : ($i === 6 ? 'final_call' : 'on_time');
            $rows[] = ['type' => 'gate', 'code' => $code, 'name' => "Gate $code", 'status' => $status];
        }
        $dfCodes = ['DF-01','DF-02','DF-03','DF-04','DF-05'];
        foreach ($dfCodes as $idx => $code) {
            $rows[] = ['type' => 'duty_free', 'code' => $code, 'name' => 'Duty‑Free Shop '.($idx+1), 'status' => $idx < 4 ? 'open' : 'closed'];
        }
        for ($i=1; $i<=9; $i++) {
            $rows[] = ['type' => 'restaurant', 'code' => sprintf('R-%02d', $i), 'name' => "Restaurant/Cafe $i", 'status' => $i <= 8 ? 'open' : 'closed'];
        }
        for ($i=1; $i<=2; $i++) {
            $rows[] = ['type' => 'lounge', 'code' => sprintf('L-%02d', $i), 'name' => "VIP Lounge $i", 'status' => 'open', 'meta' => json_encode(['occupancy' => rand(45,70)])];
        }

        // Airside
        for ($i=1; $i<=10; $i++) {
            $rows[] = ['type' => 'jet_bridge', 'code' => sprintf('JB-%02d', $i), 'name' => "Jet Bridge $i", 'status' => $i <= 4 ? 'busy' : 'open'];
        }
        for ($i=1; $i<=12; $i++) {
            $rows[] = ['type' => 'apron', 'code' => sprintf('AP-%02d', $i), 'name' => "Apron Stand $i", 'status' => $i <= 7 ? 'busy' : 'open'];
        }

        // Arrival
        for ($i=1; $i<=12; $i++) {
            $rows[] = ['type' => 'immigration_arr', 'code' => sprintf('IMA-%02d', $i), 'name' => "Arrival Immigration $i", 'status' => $i <= 8 ? 'open' : 'closed'];
        }
        for ($i=1; $i<=7; $i++) {
            $rows[] = ['type' => 'baggage_belt', 'code' => sprintf('BB-%02d', $i), 'name' => "Baggage Belt $i", 'status' => $i <= 4 ? 'busy' : 'open', 'meta' => json_encode(['inbound' => $i <= 4])];
        }

        // Additional facilities
        $rows[] = ['type' => 'info', 'code' => 'INF-01', 'name' => 'Information Desk T1', 'status' => 'open'];
        $rows[] = ['type' => 'info', 'code' => 'INF-02', 'name' => 'Information Desk T2', 'status' => 'open'];
        $rows[] = ['type' => 'info', 'code' => 'INF-03', 'name' => 'Information Desk T3', 'status' => 'open'];
        $rows[] = ['type' => 'currency', 'code' => 'FX-01', 'name' => 'Currency Exchange A', 'status' => 'open'];
        $rows[] = ['type' => 'currency', 'code' => 'FX-02', 'name' => 'Currency Exchange B', 'status' => 'closed'];
        for ($i=1; $i<=8; $i++) {
            $rows[] = ['type' => 'atm', 'code' => sprintf('ATM-%02d', $i), 'name' => "ATM $i", 'status' => $i <= 7 ? 'open' : 'closed'];
        }
        $rows[] = ['type' => 'medical', 'code' => 'MED-01', 'name' => 'Medical Center', 'status' => 'open'];
        $rows[] = ['type' => 'prayer', 'code' => 'PRY-01', 'name' => 'Prayer Room North', 'status' => 'open'];
        $rows[] = ['type' => 'prayer', 'code' => 'PRY-02', 'name' => 'Prayer Room South', 'status' => 'open'];
        for ($i=1; $i<=6; $i++) {
            $rows[] = ['type' => 'car_rental', 'code' => sprintf('CR-%02d', $i), 'name' => "Car Rental $i", 'status' => $i <= 5 ? 'open' : 'closed'];
        }
        for ($i=1; $i<=3; $i++) {
            $rows[] = ['type' => 'taxi', 'code' => sprintf('TX-%02d', $i), 'name' => "Taxi Counter $i", 'status' => 'open'];
        }
        for ($i=1; $i<=2; $i++) {
            $rows[] = ['type' => 'shuttle', 'code' => sprintf('SH-%02d', $i), 'name' => "Shuttle Service $i", 'status' => 'open'];
        }

        // Upsert by type+code
        foreach ($rows as &$r) {
            if (isset($r['meta']) && is_string($r['meta'])) {
                // keep as array for Eloquent cast when creating
                $r['meta'] = json_decode($r['meta'], true);
            }
        }
        Facility::upsert($rows, ['type','code'], ['name','status','today_count','meta']);
    }
}
