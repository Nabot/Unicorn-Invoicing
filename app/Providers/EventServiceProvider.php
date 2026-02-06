<?php

namespace App\Providers;

use App\Events\InvoiceCreated;
use App\Events\InvoiceIssued;
use App\Events\InvoiceUpdated;
use App\Events\InvoiceVoided;
use App\Events\PaymentDeleted;
use App\Events\PaymentRecorded;
use App\Listeners\LogInvoiceActivity;
use App\Listeners\LogPaymentActivity;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        InvoiceCreated::class => [
            LogInvoiceActivity::class,
        ],
        InvoiceUpdated::class => [
            LogInvoiceActivity::class,
        ],
        InvoiceIssued::class => [
            LogInvoiceActivity::class,
        ],
        InvoiceVoided::class => [
            LogInvoiceActivity::class,
        ],
        PaymentRecorded::class => [
            LogPaymentActivity::class,
        ],
        PaymentDeleted::class => [
            LogPaymentActivity::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
