<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $label = 'Invoices';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns([
                        'sm' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ])
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->options(Company::all()->pluck('name_en', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->options(Customer::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('bill_no')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('chalan_no')
                            ->maxLength(255),
                        DatePicker::make('invoice_date')
                            ->label('Invoice Date')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->format('Y-m-d'),
                        // TextInput::make('net_price')->readOnly()->label('Net Price'),
                    ]),

                Section::make()
                    ->schema([
                        Repeater::make('invoiceItems')
                            ->relationship()
                            ->columns(8)
                            ->schema([
                                TextInput::make('rq_sl')->label('R.Q S.L'),
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::orderBy('name', 'ASC')->get()->pluck('name', 'id'))
                                    ->required()
                                    ->live(debounce: 1000)
                                    ->afterStateUpdated(function ($get, $set, $state) {
                                        $product = Product::find($state);
                                        $set('brand', $product->brand ?? '');
                                        $set('mc_name', $product->mc_name ?? '');
                                        $set('p_no', $product->p_no ?? '');
                                        $qty = $get('qty');
                                        $set('price_rate', $product->price ?? '');
                                        $set('total_price', $qty * $product?->price_rate);
                                    }),

                                TextInput::make('mc_name'),
                                TextInput::make('p_no'),
                                TextInput::make('brand'),
                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->live(debounce: 1000)
                                    ->afterStateUpdated(function ($get, $set, $state) {
                                        $set('total_price', $state * $get('price_rate'));
                                    }),
                                TextInput::make('price_rate')
                                    ->label('Rate')
                                    ->numeric()
                                    ->live(debounce: 1000)
                                    ->afterStateUpdated(function ($get, $set, $state) {
                                        $set('total_price', $get('qty') * $state);
                                    }),
                                TextInput::make('total_price')->label('Amount')->numeric(),
                            ])
                            ->live()
                            ->afterStateUpdated(function ($get, $set) {
                                $items = $get('invoiceItems') ?? [];

                                $grandTotal = collect($items)->sum(function ($item) {
                                    return (float) ($item['total_price'] ?? 0);
                                });

                                $set('net_price', $grandTotal);
                            })
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bill_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('chalan_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_date'),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-printer') // Use the icon you prefer
                    ->action(function (Invoice $record) {
                        return response()->streamDownload(function () use ($record) {
                            $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $record]);
                            echo $pdf->stream();
                        }, 'invoice_' . $record->bill_no . '.pdf');
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
