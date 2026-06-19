<?php

namespace App\Filament\Teller\Resources;

use App\Filament\Teller\Resources\TransferOutResource\Pages;
use App\Models\MoneyTransferInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

use Illuminate\Support\Str;

class TransferOutResource extends Resource
{
    protected static ?string $model = MoneyTransferInvoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-right';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('message.Transfer-OUT Requests');
    }

    public static function getModelLabel(): string
    {
        return __('message.Transfer-OUT Requests');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.Transfer-OUT Requests');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('message.Transfers');
    }

    /**
     * Sidebar badge: live count of today pending + accepted
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()
            ->whereIn('status', ['pending_bkk_approval', 'accepted_bkk'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getEloquentQuery()
            ->whereIn('status', ['pending_bkk_approval', 'accepted_bkk'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $count > 0 ? 'warning' : 'success';
    }

    // ── Base Query: Transfer-OUT, today only ─────────────────────────────────

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->latest();
    }

    // ── Form (view-only) ─────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('message.Transfer Details'))
                ->schema([
                    Forms\Components\TextInput::make('invoice_number')->label(__('message.Invoice Number'))->disabled(),
                    Forms\Components\TextInput::make('customer_name')->label(__('message.Customer Name'))->disabled(),
                    Forms\Components\TextInput::make('phone')->label(__('message.Phone'))->disabled(),
                    Forms\Components\TextInput::make('bank_name')->label(__('message.Bank Name'))->disabled(),
                    Forms\Components\TextInput::make('acc_name')->label(__('message.Account Name'))->disabled(),
                    Forms\Components\TextInput::make('acc_number')->label(__('message.Account Number'))->disabled(),
                    Forms\Components\TextInput::make('currency')->label(__('message.Currency'))->disabled(),
                    Forms\Components\TextInput::make('entered_amount')->label(__('message.Entered Amount'))->disabled(),
                    Forms\Components\TextInput::make('trf_fee')->label(__('message.Transfer Fee'))->disabled(),
                    Forms\Components\TextInput::make('net_amount')->label(__('message.Net Amount'))->disabled(),
                    Forms\Components\Select::make('status')
                        ->label(__('message.Status'))
                        ->options([
                            'pending_bkk_approval' => __('message.Pending'),
                            'accepted_bkk'         => __('message.Accepted'),
                            'completed'            => __('message.Completed'),
                            'Rejected'             => __('message.Rejected'),
                            'cancelled'            => __('message.Cancelled'),
                        ])->disabled(),
                    Forms\Components\Textarea::make('reject_reason')->label(__('message.Reject Reason'))->disabled(),
                ])->columns(2),
        ]);
    }

    // ── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('message.Serial number'))
                    ->rowIndex()
                    ->searchable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('message.Time'))
                    ->dateTime('d M Y h:i')
                    ->searchable()
                    ->color('gray'),
                TextColumn::make('invoice_number')
                    ->label(__('message.Invoice Number'))
                    ->searchable()->sortable()->copyable()
                    ->weight('bold')->color('primary'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('message.Status'))
                    ->searchable()
                    ->colors([
                        'warning' => 'pending_bkk_approval',
                        'success' => 'accepted_bkk',
                        'primary' => 'completed',
                        'danger'  => 'Rejected',
                        'gray'    => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending_bkk_approval' => '⏳ ' . __('message.Pending'),
                        'accepted_bkk'         => '✅ ' . __('message.Accepted'),
                        'completed'            => '✔ '  . __('message.Completed'),
                        'Rejected'             => '❌ ' . __('message.Rejected'),
                        'cancelled'            => '🚫 ' . __('message.Cancelled'),
                        default                => $state,
                    })->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label(__('message.Customer name'))
                    ->html()
                    ->searchable()
                    ->getStateUsing(fn ($record) =>
                        '<strong>' . e(Str::ucfirst($record->customer_name ?? '')) . '</strong><br>' .
                        '<span style="font-size:12px;color:#94a3b8">' . e($record->phone ?? '') . '</span>'
                    ),
                TextColumn::make('bank_name')
                    ->label(__('message.Bank Details'))
                    ->html()
                    ->searchable()
                    ->formatStateUsing(fn ($state, $record) =>
                        '<div style="line-height:1.6">
                            <strong>' . e($record->bank_name ?? '—') . '</strong><br>
                            <span style="font-size:12px;color:#94a3b8">' . e($record->acc_number ?? '') . '</span><br>
                            <span style="font-size:11px;color:#64748b">' . e($record->acc_name ?? '') . '</span>
                        </div>'
                    ),
                TextColumn::make('entered_amount')
                    ->label(__('message.Amount'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->currency . ' ' . $state;
                    })
                    ->searchable()
                    ->alignRight(),
                TextColumn::make('trf_fee')
                    ->label(__('message.Transfer Fee'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->currency . ' ' . $state;
                    })
                    ->searchable(),
                TextColumn::make('net_amount')
                    ->label(__('message.Net Amount'))
                    ->formatStateUsing(function ($state, $record) {
                        return $record->currency . ' ' . $state;
                    })
                    ->searchable()
                    ->weight('bold')->color('success'),
                TextColumn::make('reject_reason')
                    ->label(__('message.Reject Reason'))
                    ->limit(30)->placeholder('—')
                    ->color('danger')
                    ->searchable()
                    ->tooltip(fn ($record) => $record?->reject_reason),

            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('5s')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('message.Status'))
                    ->options([
                        'pending_bkk_approval' => __('message.Pending'),
                        'accepted_bkk'         => __('message.Accepted'),
                        'completed'            => __('message.Completed'),
                        'Rejected'             => __('message.Rejected'),
                        'cancelled'            => __('message.Cancelled'),
                    ]),
            ])

            // ── Row Actions ──────────────────────────────────────────────────
            ->actions([

                // 1. View Invoice (new tab)
                Action::make('view_invoice')
                    ->label(__('message.View Invoice'))
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->tooltip(__('message.View Invoice'))
                    ->url(fn (MoneyTransferInvoice $record): string => $record->invoice_url ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (MoneyTransferInvoice $record): bool => !empty($record->invoice_url)),

                // 2. View + Download Transaction Slip
                Action::make('view_slip')
                    ->label(__('message.Transaction Slip'))
                    ->icon('heroicon-o-photo')
                    ->color('success')
                    ->tooltip(__('message.Transaction Slip'))
                    ->modalHeading(__('message.Transaction Slip'))
                    ->modalContent(function (MoneyTransferInvoice $record) {
                        if (empty($record->transaction_slip)) {
                            return view('filament.teller.modals.no-slip', [
                                'message' => __('message.No slip uploaded'),
                            ]);
                        }
                        return view('filament.teller.modals.view-slip', [
                            'slipUrl'       => Storage::url($record->transaction_slip),
                            'invoiceNumber' => $record->invoice_number,
                            'downloadLabel' => __('message.Download Slip'),
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('message.Close'))
                    ->visible(fn (MoneyTransferInvoice $record): bool => !empty($record->transaction_slip)),

                // ✅ Reject with dropdown + optional textarea
                Action::make('reject')
                    ->label(__('message.Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading(__('message.Reject Transfer-OUT Request'))
                    ->modalSubmitActionLabel(__('message.Yes, Reject'))
                    ->modalCancelActionLabel(__('message.Cancel'))
                    ->modalWidth('md')
                    ->form([
                        Forms\Components\Select::make('reject_category')
                            ->label(__('message.Reason for Rejection'))
                            ->options([
                                'Wrong Account details' => __('message.Wrong Account details'),
                                'Wrong amount'          => __('message.Wrong amount'),
                                'Change mind'           => __('message.Change mind'),
                                'other'                 => __('message.Other (specify below)'),
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Textarea::make('reject_reason_text')
                            ->label(__('message.Specify Reason'))
                            ->placeholder(__('message.Enter detailed reason...'))
                            ->rows(3)
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('reject_category') === 'other'),
                    ])
                    ->action(function (MoneyTransferInvoice $record, array $data) {
                        $reason = $data['reject_category'] === 'other'
                            ? ($data['reject_reason_text'] ?? __('message.Other'))
                            : $data['reject_category'];

                        $record->update([
                            'status'        => 'Rejected',
                            'reject_reason' => $reason,
                        ]);

                        Notification::make()
                            ->title('❌ ' . __('message.Transfer-IN Rejected'))
                            ->body(__('message.Invoice') . " {$record->invoice_number} " . __('message.has been rejected.') . ' ' . __('message.Reason') . ': ' . $reason)
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (MoneyTransferInvoice $record): bool =>
                        in_array($record->status, ['pending_bkk_approval', 'accepted_bkk'])
                    ),
            ])

            // ── Header Actions: Export Excel + PDF ───────────────────────────
            ->headerActions([
                // Export to Excel — respects active filters
                ExportAction::make('export_excel')
                    ->label(__('message.Export'))
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->exports([
                         ExcelExport::make()->fromTable()->except([
                            'Serial_number', 'updated_at',
                        ]),
                    ]),
            ])

            // ── Bulk Actions ─────────────────────────────────────────────────
            ->bulkActions([

                 ExportBulkAction::make('bulk_export_excel')
                    ->label(__('message.Export'))
                    ->color('success')
                    ->exports([
                         ExcelExport::make()->fromTable()->except([
                            'Serial_number', 'updated_at',
                        ]),
                    ]),
            ])

            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateHeading(__('message.Today Transfer-OUT'))
            ->emptyStateDescription(__('message.No Transfer-OUT requests today'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferOuts::route('/'),
        ];
    }

    public static function canCreate(): bool        { return false; }
    public static function canEdit($record): bool   { return false; }
    public static function canDelete($record): bool { return false; }
}