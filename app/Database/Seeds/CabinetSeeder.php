<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CabinetSeeder extends Seeder
{
    public function run()
    {
        $csvFile = ROOTPATH . '../cablocations.csv';

        if (! file_exists($csvFile)) {
            echo "CSV file not found at: {$csvFile}\n";
            return;
        }

        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle); // skip header row

        $batch  = [];
        $count  = 0;
        $batchSize = 500;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 7) continue;

            $batch[] = [
                'db'       => trim($row[0]),
                'db_name'  => trim($row[1]),
                'exchange' => trim($row[2]),
                'cab'      => trim($row[3]),
                'address'  => trim($row[4]),
                'lat'      => is_numeric(trim($row[5])) ? (float) trim($row[5]) : null,
                'lng'      => is_numeric(trim($row[6])) ? (float) trim($row[6]) : null,
                'notes'    => isset($row[7]) ? trim($row[7]) : null,
            ];

            $count++;

            if (count($batch) >= $batchSize) {
                $this->db->table('cabinets')->insertBatch($batch);
                $batch = [];
                echo "Inserted {$count} records...\n";
            }
        }

        if (! empty($batch)) {
            $this->db->table('cabinets')->insertBatch($batch);
        }

        fclose($handle);
        echo "Done! Total records inserted: {$count}\n";
    }
}
