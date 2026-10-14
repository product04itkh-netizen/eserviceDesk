<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-time ETL: copies the real production dataset (imported into the local
 * `mysql_legacy` MariaDB staging DB from Jea's mysqldump export) into the
 * `pgsql` connection (Supabase dev project). Run against a freshly migrated,
 * empty Postgres schema only — `php artisan migrate:fresh` first, no seeders.
 *
 * Table order is FK-dependency-safe (parents before children). Small/pivot
 * tables are loaded in one pass; large id-keyed tables use chunkById to keep
 * memory bounded and avoid the offset-based chunk() row-skipping trap.
 */
class MigrateMysqlDumpToPostgres extends Command
{
    protected $signature = 'etl:migrate-real-data {--chunk=2000} {--from=} {--sequences-only}';

    protected $description = 'One-time ETL of the real eService production dataset from mysql_legacy into pgsql';

    /** Tables with no single 'id' PK, or trivially small — always loaded via a single get(). */
    protected $smallOrNoId = [
        'roles', 'permissions', 'model_has_roles', 'model_has_permissions',
        'role_has_permissions', 'groupproblem_problem',
    ];

    /** FK-dependency-safe load order. */
    protected $tables = [
        'users', 'roles', 'permissions', 'model_has_roles', 'model_has_permissions',
        'role_has_permissions', 'password_histories',
        'customers', 'vendors', 'problems', 'brands', 'categories', 'items',
        'group_problems', 'groupproblem_problem', 'rmacodes', 'enginner_busys',
        'services', 'services_status', 'service_rmas', 'problem_service', 'service_brand',
        'transfers', 'service_transfer', 'shippings', 'service_shipping',
        'purchases', 'purchases_details', 'sale_orders', 'sale_order_details',
        'audits',
    ];

    public function handle()
    {
        if ($this->option('sequences-only')) {
            $this->resetSequences();
            return 0;
        }

        $chunkSize = (int) $this->option('chunk');
        $src = DB::connection('mysql_legacy');
        $dst = DB::connection('pgsql');

        $results = [];
        $from = $this->option('from');
        $started = empty($from);

        foreach ($this->tables as $table) {
            if (!$started) {
                if ($table === $from) {
                    $started = true;
                } else {
                    $this->line("{$table}: skipped (before --from={$from})");
                    continue;
                }
            }
            if (!Schema::connection('pgsql')->hasTable($table)) {
                $this->error("Abort: destination table '{$table}' does not exist. Run migrate:fresh first.");
                return 1;
            }

            $srcCount = $src->table($table)->count();
            if ($srcCount === 0) {
                $results[$table] = [0, 0, 'empty source'];
                $this->line("{$table}: empty source, skipped");
                continue;
            }

            $boolCols = $this->booleanColumns($table);

            $inserted = 0;
            if (in_array($table, $this->smallOrNoId, true) || $srcCount <= 5000) {
                $src->table($table)->get()->chunk(500)->each(function ($chunk) use ($dst, $table, $boolCols, &$inserted) {
                    $batch = $chunk->map(fn ($row) => $this->castRow((array) $row, $boolCols))->all();
                    $dst->table($table)->insert($batch);
                    $inserted += count($batch);
                });
            } else {
                $src->table($table)->orderBy('id')->chunkById($chunkSize, function ($rows) use ($dst, $table, $boolCols, &$inserted) {
                    $batch = $rows->map(fn ($row) => $this->castRow((array) $row, $boolCols))->all();
                    $dst->table($table)->insert($batch);
                    $inserted += count($batch);
                });
            }

            $dstCount = $dst->table($table)->count();
            $status = $srcCount === $dstCount ? 'OK' : 'MISMATCH';
            $results[$table] = [$srcCount, $dstCount, $status];
            $this->line("{$table}: source={$srcCount} dest={$dstCount} inserted={$inserted} {$status}");

            if ($status === 'MISMATCH') {
                $this->error("Row count mismatch on {$table} — stopping for inspection.");
                $this->printSummary($results);
                return 1;
            }
        }

        $this->resetSequences();

        $this->newLine();
        $this->printSummary($results);

        return 0;
    }

    protected function booleanColumns(string $table): array
    {
        $bool = [];
        foreach (Schema::connection('pgsql')->getColumnListing($table) as $col) {
            try {
                if (Schema::connection('pgsql')->getColumnType($table, $col) === 'boolean') {
                    $bool[] = $col;
                }
            } catch (\Throwable $e) {
                // unresolvable type (rare custom type) — leave as-is
            }
        }
        return $bool;
    }

    protected function castRow(array $row, array $boolCols): array
    {
        foreach ($row as $k => $v) {
            if ($v === '0000-00-00' || $v === '0000-00-00 00:00:00') {
                $row[$k] = null;
            }
        }
        foreach ($boolCols as $bc) {
            if (array_key_exists($bc, $row) && $row[$bc] !== null) {
                $row[$bc] = (bool) $row[$bc];
            }
        }
        return $row;
    }

    protected function resetSequences(): void
    {
        foreach ($this->tables as $table) {
            if (in_array($table, ['model_has_roles', 'model_has_permissions', 'role_has_permissions', 'groupproblem_problem'], true)) {
                continue; // no serial PK
            }
            $max = DB::connection('pgsql')->table($table)->max('id');
            if ($max === null) {
                continue;
            }
            DB::connection('pgsql')->statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), {$max})");
        }
        $this->info('Sequences reset to MAX(id) for every serial-PK table.');
    }

    protected function printSummary(array $results): void
    {
        $rows = [];
        foreach ($results as $table => [$s, $d, $status]) {
            $rows[] = [$table, $s, $d, $status];
        }
        $this->table(['table', 'source', 'dest', 'status'], $rows);
    }
}
