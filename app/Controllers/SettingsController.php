<?php

namespace App\Controllers;

use App\Models\ApiKeyModel;
use App\Models\CabinetModel;
use App\Models\SettingsModel;

class SettingsController extends BaseController
{
    private SettingsModel $settings;
    private ApiKeyModel   $apiKeys;
    private CabinetModel  $cabinets;

    public function __construct()
    {
        $this->settings = new SettingsModel();
        $this->apiKeys  = new ApiKeyModel();
        $this->cabinets = new CabinetModel();
    }

    // -------------------------------------------------------------------------
    // GET /settings  — General
    // -------------------------------------------------------------------------
    public function general(): string
    {
        return view('settings/general', [
            'title'      => 'Settings — PCP Locations',
            'totalCount' => $this->cabinets->getTotalCount(),
            'success'    => session()->getFlashdata('success'),
            'errors'     => session()->getFlashdata('errors'),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /settings
    // -------------------------------------------------------------------------
    public function saveGeneral(): \CodeIgniter\HTTP\RedirectResponse
    {
        $title = trim($this->request->getPost('site_title') ?? '');

        if ($title === '') {
            return redirect()->to('/settings')->with('errors', ['Site title cannot be empty.']);
        }

        $this->settings->saveSetting('site_title', $title);

        // Viewer rate limit
        $rateLimit = (int) ($this->request->getPost('viewer_rate_limit') ?? 100);
        $this->settings->saveSetting('viewer_rate_limit', (string) max(1, $rateLimit));

        // Logo upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && ! $logo->hasMoved()) {
            if (! in_array(strtolower($logo->getClientExtension()), ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'])) {
                return redirect()->to('/settings')->with('errors', ['Logo must be an image file (PNG, JPG, GIF, SVG, WEBP).']);
            }
            $this->deleteSettingFile('logo_path');
            $name = 'logo_' . time() . '.' . $logo->getClientExtension();
            $logo->move(FCPATH . 'uploads/settings', $name);
            $this->settings->saveSetting('logo_path', $name);
        }

        // Favicon — SVG string takes priority over file upload
        $faviconSvg = trim($this->request->getPost('favicon_svg') ?? '');
        $favicon    = $this->request->getFile('favicon');

        if ($faviconSvg !== '') {
            // Basic check that it looks like an SVG
            if (stripos($faviconSvg, '<svg') === false) {
                return redirect()->to('/settings')->with('errors', ['SVG string must contain a valid <svg> element.']);
            }
            $this->deleteSettingFile('favicon_path');
            $name = 'favicon_' . time() . '.svg';
            file_put_contents(FCPATH . 'uploads/settings/' . $name, $faviconSvg);
            $this->settings->saveSetting('favicon_path', $name);
        } elseif ($favicon && $favicon->isValid() && ! $favicon->hasMoved()) {
            if (! in_array(strtolower($favicon->getClientExtension()), ['ico', 'png', 'svg'])) {
                return redirect()->to('/settings')->with('errors', ['Favicon must be .ico, .png, or .svg.']);
            }
            $this->deleteSettingFile('favicon_path');
            $name = 'favicon_' . time() . '.' . $favicon->getClientExtension();
            $favicon->move(FCPATH . 'uploads/settings', $name);
            $this->settings->saveSetting('favicon_path', $name);
        }

        // Remove logo
        if ($this->request->getPost('remove_logo')) {
            $this->deleteSettingFile('logo_path');
            $this->settings->saveSetting('logo_path', null);
        }

        // Remove favicon
        if ($this->request->getPost('remove_favicon')) {
            $this->deleteSettingFile('favicon_path');
            $this->settings->saveSetting('favicon_path', null);
        }

        return redirect()->to('/settings')->with('success', 'Settings saved.');
    }

    // -------------------------------------------------------------------------
    // GET /settings/import
    // -------------------------------------------------------------------------
    public function import(): string
    {
        return view('settings/import', [
            'title'      => 'Settings — PCP Locations',
            'totalCount' => $this->cabinets->getTotalCount(),
            'errors'     => session()->getFlashdata('errors'),
            'result'     => session()->getFlashdata('result'),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /settings/import
    // -------------------------------------------------------------------------
    public function importStore(): \CodeIgniter\HTTP\RedirectResponse
    {
        $file = $this->request->getFile('csv_file');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->to('/settings/import')->with('errors', ['No valid file uploaded.']);
        }

        if (strtolower($file->getClientExtension()) !== 'csv') {
            return redirect()->to('/settings/import')->with('errors', ['File must be a CSV.']);
        }

        $handle = fopen($file->getTempName(), 'r');

        if ($handle === false) {
            return redirect()->to('/settings/import')->with('errors', ['Could not read uploaded file.']);
        }

        $rawHeader = fgetcsv($handle);
        if ($rawHeader === false) {
            fclose($handle);
            return redirect()->to('/settings/import')->with('errors', ['CSV file is empty or unreadable.']);
        }

        $header   = array_map('strtolower', array_map('trim', $rawHeader));
        $required = ['db', 'db_name', 'exchange', 'cab'];
        $missing  = array_diff($required, $header);

        if (! empty($missing)) {
            fclose($handle);
            return redirect()->to('/settings/import')
                ->with('errors', ['Missing required columns: ' . implode(', ', $missing)]);
        }

        $col    = array_flip($header);
        $lngCol = $col['lng'] ?? $col['long'] ?? null;

        // Build existing key set for duplicate detection
        $existing = [];
        foreach ($this->cabinets->select('db, exchange, cab')->findAll() as $r) {
            $existing[strtoupper($r['db']) . '|' . strtoupper($r['exchange']) . '|' . strtoupper($r['cab'])] = true;
        }

        $inserted  = 0;
        $skipped   = 0;
        $invalid   = 0;
        $batch     = [];
        $batchSize = 500;

        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, 'strlen')) === 0) continue;

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

        return redirect()->to('/settings/import')->with('result', [
            'inserted' => $inserted,
            'skipped'  => $skipped,
            'invalid'  => $invalid,
            'filename' => esc($file->getClientName()),
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /settings/apikeys
    // -------------------------------------------------------------------------
    public function apiKeys(): string
    {
        return view('settings/apikeys', [
            'title'      => 'Settings — PCP Locations',
            'totalCount' => $this->cabinets->getTotalCount(),
            'keys'       => $this->apiKeys->orderBy('created_at', 'DESC')->findAll(),
            'success'    => session()->getFlashdata('success'),
            'new_key'    => session()->getFlashdata('new_key'),
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /settings/apikeys/new
    // -------------------------------------------------------------------------
    public function apiKeyNew(): string
    {
        return view('settings/apikeys_new', [
            'title'      => 'Settings — PCP Locations',
            'totalCount' => $this->cabinets->getTotalCount(),
            'error'      => session()->getFlashdata('error'),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /settings/apikeys/new
    // -------------------------------------------------------------------------
    public function apiKeyStore(): \CodeIgniter\HTTP\RedirectResponse
    {
        $name = trim($this->request->getPost('name') ?? '');

        if ($name === '') {
            return redirect()->back()->withInput()->with('error', 'Name is required.');
        }

        $key = $this->apiKeys->generateKey();

        $this->apiKeys->insert([
            'name'       => $name,
            'api_key'    => $key,
            'active'     => 1,
            'created_by' => session()->get('user_id'),
        ]);

        session()->setFlashdata('new_key', $key);

        return redirect()->to('/settings/apikeys');
    }

    // -------------------------------------------------------------------------
    // POST /settings/apikeys/revoke/:id
    // -------------------------------------------------------------------------
    public function apiKeyRevoke(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $this->apiKeys->update($id, ['active' => 0]);
        return redirect()->to('/settings/apikeys')->with('success', 'Key revoked.');
    }

    // -------------------------------------------------------------------------
    // POST /settings/apikeys/delete/:id
    // -------------------------------------------------------------------------
    public function apiKeyDelete(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $this->apiKeys->delete($id);
        return redirect()->to('/settings/apikeys')->with('success', 'Key deleted.');
    }

    // -------------------------------------------------------------------------
    private function deleteSettingFile(string $settingKey): void
    {
        $existing = setting($settingKey);
        if ($existing) {
            $path = FCPATH . 'uploads/settings/' . $existing;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
