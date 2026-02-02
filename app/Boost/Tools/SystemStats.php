<?php

namespace App\Boost\Tools;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Laravel\Boost\Attributes\Tool;
use Laravel\Boost\Contracts\ToolInterface;

/**
 * SystemStats MCP Tool
 * 
 * Provides system information to AI agents including:
 * - Laravel version
 * - PHP version  
 * - Database connection status
 */
#[Tool(
    name: 'system_stats',
    description: 'Get system information including Laravel version, PHP version, and database connection status'
)]
class SystemStats implements ToolInterface
{
    /**
     * Get system statistics and information.
     *
     * @return array
     */
    public function handle(): array
    {
        return [
            'laravel_version' => App::version(),
            'php_version' => PHP_VERSION,
            'database_status' => $this->getDatabaseStatus(),
            'environment' => App::environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
        ];
    }

    /**
     * Check database connection status.
     *
     * @return array
     */
    protected function getDatabaseStatus(): array
    {
        try {
            DB::connection()->getPdo();
            $connected = true;
            $driver = DB::connection()->getDriverName();
            $database = DB::connection()->getDatabaseName();
            $error = null;
        } catch (\Exception $e) {
            $connected = false;
            $driver = null;
            $database = null;
            $error = $e->getMessage();
        }

        return [
            'connected' => $connected,
            'driver' => $driver,
            'database' => $database,
            'error' => $error,
        ];
    }

    /**
     * Get the tool's input schema for validation.
     *
     * @return array
     */
    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
            'required' => [],
        ];
    }
}
