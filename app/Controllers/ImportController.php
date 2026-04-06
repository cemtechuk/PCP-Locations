<?php

namespace App\Controllers;

use App\Models\CabinetModel;

class ImportController extends BaseController
{
    private CabinetModel $cabinets;

    public function __construct()
    {
        $this->cabinets = new CabinetModel();
    }

    public function index(): string
    {
        return view('import/index', [
            'title'      => 'Import Data — PCP Locations',
            'totalCount' => $this->cabinets->getTotalCount(),
            'errors'     => session()->getFlashdata('errors'),
            'result'     => session()->getFlashdata('result'),
        ]);
    }

    public function store(): \CodeIgniter\HTTP\RedirectResponse
    {
        $file = $this->request->getFile('csv_file');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->to('/import')->with('errors', ['No valid file uploaded.']);
        }

        if (strtolower($file->getClientExtension()) !== 'csv') {
            return redirect()->to('/import')->with('errors', ['File must be a CSV.']);
        }

        $handle = fopen($file->getTempName(), 'r');

        if ($handle === false) {
            return redirect()->to('/import')->with('errors', ['Could not read uploaded file.']);
        }

        // Read header row and normalise to lowercase for flexible column mapping
        $rawHeader = fgetcsv($handle);
        if ($rawHeader === false) {
            fclose($handle);
            return redirect()->to('/import')->with('errors', ['CSV file is empty or unreadable.']);
        }

        $header = array_map('strtolower', array_map('trim', $rawHeader));

        // Required columns (accept both 'long' from scraper and 'lng' from manual CSVs)
        $required = ['db', 'db_name', 'exchange', 'cab'];
        $missing  = array_diff($required, $header);

        if (! empty($missing)) {
            fclose($handle);
            return redirect()->to('/import')
                ->with('errors', ['Missing required columns: ' . implode(', ', $missing)]);
        }

        // Column index map
        $col = array_flip($header);
        $lngCol = $col['lng'] ?? $col['long'] ?? null;

        // Build a set of existing (db, exchange, cab) to detect duplicates efficiently
        $existing = [];
        $rows = $this->cabinets->select('db, exchange, cab')->findAll();
        foreach ($rows as $r) {
            $existing[strtoupper($r['db']) . '|' . strtoupper($r['exchange']) . '|' . strtoupper($r['cab'])] = true;
        }

        $inserted  = 0;
        $skipped   = 0;
        $invalid   = 0;
        $batch     = [];
        $batchSize = 500;

        while (($row = fgetcsv($handle)) !== false) {
            // Skip blank lines
            if (count(array_filter($row, 'strlen')) === 0) {
                continue;
            }

            $db       = strtoupper(trim($row[$col['db']]       ?? ''));
            $dbName   = trim($row[$col['db_name']]             ?? '');
            $exchange = strtoupper(trim($row[$col['exchange']] ?? ''));
            $cab      = strtoupper(trim($row[$col['cab']]      ?? ''));

            if ($db === '' || $dbName === '' || $exchange === '' || $cab === '') {
                $invalid++;
                continue;
            }

            $key = "{$db}|{$exchange}|{$cab}";

            if (isset($existing[$key])) {
                $skipped++;
                continue;
            }

            $latRaw = trim($row[$col['lat']]      ?? '');
            $lngRaw = $lngCol !== null ? trim($row[$lngCol] ?? '') : '';

            $batch[] = [
                'db'       => $db,
                'db_name'  => $dbName,
                'exchange' => $exchange,
                'cab'      => $cab,
                'address'  => trim($row[$col['address']] ?? ''),
                'lat'      => is_numeric($latRaw) ? (float) $latRaw : null,
                'lng'      => is_numeric($lngRaw) ? (float) $lngRaw : null,
                'notes'    => isset($col['notes']) ? trim($row[$col['notes']] ?? '') : null,
            ];

            // Track in local set so duplicates within the same file are also caught
            $existing[$key] = true;
            $inserted++;

            if (count($batch) >= $batchSize) {
                $this->cabinets->insertBatch($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            $this->cabinets->insertBatch($batch);
        }

        fclose($handle);

        return redirect()->to('/import')->with('result', [
            'inserted' => $inserted,
            'skipped'  => $skipped,
            'invalid'  => $invalid,
            'filename' => esc($file->getClientName()),
        ]);
    }
}
