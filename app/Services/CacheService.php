<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache client list for a company.
     */
    public static function getClients(int $companyId, callable $callback, int $ttl = 3600)
    {
        $key = "clients:company:{$companyId}";
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Clear client cache for a company.
     */
    public static function clearClients(int $companyId): void
    {
        Cache::forget("clients:company:{$companyId}");
    }

    /**
     * Cache invoice statistics for a company.
     */
    public static function getInvoiceStats(int $companyId, callable $callback, int $ttl = 1800)
    {
        $key = "invoice_stats:company:{$companyId}";
        
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Clear invoice stats cache for a company.
     */
    public static function clearInvoiceStats(int $companyId): void
    {
        Cache::forget("invoice_stats:company:{$companyId}");
    }

    /**
     * Clear all caches for a company.
     */
    public static function clearCompanyCache(int $companyId): void
    {
        self::clearClients($companyId);
        self::clearInvoiceStats($companyId);
    }

    /**
     * Retrieve an item from the cache, or store the default value if it doesn't exist.
     *
     * @param  string  $key
     * @param  int  $ttl  Time to live in seconds
     * @param  callable  $callback
     * @return mixed
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        return Cache::remember($key, $ttl, $callback);
    }
}
