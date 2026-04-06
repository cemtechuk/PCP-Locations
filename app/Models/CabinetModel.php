<?php

namespace App\Models;

use CodeIgniter\Model;

class CabinetModel extends Model
{
    protected $table         = 'cabinets';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['db', 'db_name', 'exchange', 'cab', 'address', 'lat', 'lng', 'notes'];

    /**
     * Live exchange search — returns exchanges matching the query.
     * Pulls the EXCH row's coordinates via conditional aggregation so the
     * Maps link points to the exchange building, not a cabinet.
     */
    public function searchExchanges(string $query): array
    {
        $builder = $this->db->table('cabinets');

        if ($query !== '') {
            $builder->like('exchange', $query);
        }

        return $builder
            ->select("db, db_name, exchange,
                      COUNT(*) AS cabinet_count,
                      MAX(CASE WHEN UPPER(cab) = 'EXCH' THEN lat END) AS exch_lat,
                      MAX(CASE WHEN UPPER(cab) = 'EXCH' THEN lng END) AS exch_lng")
            ->groupBy('db, db_name, exchange')
            ->orderBy('exchange', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * All cabinets for a given exchange, with optional cabinet/address filter.
     */
    public function getCabinetsForExchange(string $db, string $exchange, string $filter = ''): array
    {
        $builder = $this->db->table('cabinets')
            ->where('db', $db)
            ->where('exchange', $exchange);

        if ($filter !== '') {
            $builder->groupStart()
                ->like('cab', $filter)
                ->orLike('address', $filter)
                ->groupEnd();
        }

        return $builder
            ->orderBy("LEFT(cab, 1) ASC, CAST(SUBSTRING(cab, 2) AS UNSIGNED) ASC", '', false)
            ->get()
            ->getResultArray();
    }

    /**
     * Summary info for a single exchange, including EXCH building coordinates.
     */
    public function getExchangeInfo(string $db, string $exchange): array|null
    {
        $row = $this->db->table('cabinets')
            ->select("db, db_name, exchange,
                      COUNT(*) AS cabinet_count,
                      MAX(CASE WHEN UPPER(cab) = 'EXCH' THEN lat END) AS exch_lat,
                      MAX(CASE WHEN UPPER(cab) = 'EXCH' THEN lng END) AS exch_lng")
            ->where('db', $db)
            ->where('exchange', $exchange)
            ->groupBy('db, db_name, exchange')
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Returns the 3 nearest exchanges to the given coordinates,
     * using the Haversine formula against each exchange's EXCH row.
     */
    public function getNearbyExchanges(float $lat, float $lng, int $limit = 3): array
    {
        $sql = "
            SELECT db, db_name, exchange, cabinet_count, exch_lat, exch_lng,
                   ROUND(
                       6371 * ACOS(LEAST(1.0,
                           COS(RADIANS(?)) * COS(RADIANS(exch_lat)) *
                           COS(RADIANS(exch_lng) - RADIANS(?)) +
                           SIN(RADIANS(?)) * SIN(RADIANS(exch_lat))
                       )), 2
                   ) AS distance_km
            FROM (
                SELECT db, db_name, exchange,
                       COUNT(*) AS cabinet_count,
                       MAX(CASE WHEN UPPER(cab) = 'EXCH' THEN lat END) AS exch_lat,
                       MAX(CASE WHEN UPPER(cab) = 'EXCH' THEN lng END) AS exch_lng
                FROM cabinets
                GROUP BY db, db_name, exchange
            ) AS exc
            WHERE exch_lat IS NOT NULL
            ORDER BY distance_km ASC
            LIMIT ?
        ";

        return $this->db->query($sql, [$lat, $lng, $lat, $limit])->getResultArray();
    }

    public function getRegions(): array
    {
        return $this->db->table('cabinets')
            ->select('db, db_name')
            ->distinct()
            ->orderBy('db_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function exchangeExists(string $db, string $exchange): bool
    {
        return $this->db->table('cabinets')
            ->where('db', $db)
            ->where('exchange', $exchange)
            ->countAllResults() > 0;
    }

    public function getTotalCount(): int
    {
        return $this->countAll();
    }

    /**
     * Flat cabinet search — fuzzy exchange name, exact cabinet number.
     * Allows callers to find a specific cabinet without knowing the db/region.
     */
    public function searchCabinet(string $exchange, string $cab): array
    {
        return $this->db->table('cabinets')
            ->like('exchange', $exchange)
            ->where('UPPER(cab)', strtoupper($cab))
            ->orderBy('exchange', 'ASC')
            ->get()
            ->getResultArray();
    }
}
