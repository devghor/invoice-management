<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\Customer;
use App\Models\Product;
use Filament\Forms\Components\Section;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Overview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Section::make('Total Products', Product::count())
                ->description('All products available')
                ->descriptionIcon('heroicon-o-cube')
                ->color('success'),

            Section::make('Total Customers', Customer::count())
                ->description('Registered customers')
                ->descriptionIcon('heroicon-o-user')
                ->color('primary'),
        ];
    }
}
