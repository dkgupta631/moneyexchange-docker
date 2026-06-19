<?php

namespace App\Filament\Resources;

use App\Exports\TransferInReportExport;
use App\Filament\Resources\TransferInReportResource\Pages;
use App\Models\MoneyTransferInvoice;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class TransferInReportResource extends Resource
{
    protected static ?string $model = MoneyTransferInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-left';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('message.transfer_in_report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.transfer_in_report');
    }

    public static function getModelLabel(): string
    {
        return __('message.transfer_in_record');
    }

    public static function getNavigationBadge(): ?string
    {
            $count = MoneyTransferInvoice::where('transfer_type', 'Transfer-IN')
                        ->whereDate('created_at', today())
                        ->count();

            return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('transfer_type', 'Transfer-IN')
            ->orderBy('id', 'desc');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->columns([
                // ✅ Use sortable id — NO rowIndex() to avoid export crash
                Tables\Columns\TextColumn::make('id')
                    ->label(__('message.Serial number'))
                    ->sortable()
                    ->rowIndex()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('message.Time'))
                    ->dateTime('d M Y H:i')
                    ->searchable()
                    ->sortable()
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small),

                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('message.Invoice Number'))
                    ->searchable()
                    ->copyable()
                    ->color('primary')
                    ->weight('bold')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('message.Status'))
                    ->badge()
                    ->searchable()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'             => 'success',
                        'pending_bkk_approval'  => 'warning',
                        'accepted_bkk'          => 'info',
                        'Rejected', 'cancelled' => 'danger',
                        default                 => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending_bkk_approval' => '⏳ ' . __('message.Pending'),
                        'accepted_bkk'         => '✅ ' . __('message.Accepted'),
                        'completed'            => '✔ '  . __('message.Completed'),
                        'Rejected'             => '❌ ' . __('message.Rejected'),
                        'cancelled'            => '🚫 ' . __('message.Cancelled'),
                        default                => $state,
                    }),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label(__('message.Customer name'))
                    ->html()
                    ->searchable()
                    ->getStateUsing(fn ($record) =>
                        '<strong>' . e(Str::ucfirst($record->customer_name ?? '—')) . '</strong><br>' .
                        '<span style="font-size:12px;color:#94a3b8">' . e($record->phone ?? '') . '</span>'
                    ),

                Tables\Columns\TextColumn::make('bank_name')
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

                Tables\Columns\TextColumn::make('entered_amount')
                    ->label(__('message.Amount'))
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->currency ?? '') . ' ' . number_format((float) $state, 2)
                    )
                    ->color('warning')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('trf_fee')
                    ->label(__('message.Transfer Fee'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->currency ?? '') . ' ' . number_format((float) $state, 2)
                    )
                    ->color('gray'),

                Tables\Columns\TextColumn::make('net_amount')
                    ->label(__('message.Net Amount'))
                    ->searchable()
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->currency ?? '') . ' ' . number_format((float) $state, 2)
                    )
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('reject_reason')
                    ->label(__('message.Reject Reason'))
                    ->searchable()
                    ->default('—')
                    ->color('danger')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('message.Status'))
                    ->options([
                        'pending_bkk_approval' => '⏳ ' . __('message.Pending'),
                        'accepted_bkk'         => '✅ ' . __('message.Accepted'),
                        'completed'            => '✔ '  . __('message.Completed'),
                        'Rejected'             => '❌ ' . __('message.Rejected'),
                        'cancelled'            => '🚫 ' . __('message.Cancelled'),
                    ])
                    ->placeholder(__('message.All Statuses')),
            ])
            ->actions([
                // Action 1: View Invoice URL in new tab
                Action::make('view_invoice')
                    ->label(__('message.View Invoice'))
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (MoneyTransferInvoice $record) => $record->invoice_url)
                    ->openUrlInNewTab()
                    ->visible(fn (MoneyTransferInvoice $record) => ! empty($record->invoice_url)),

                // Action 2: View Transaction Slip in new tab
                Action::make('view_slip')
                    ->label(__('message.Transaction Slip'))
                    ->icon('heroicon-o-photo')
                    ->color('success')
                    ->url(fn (MoneyTransferInvoice $record) => asset('storage/' . $record->transaction_slip))
                    ->openUrlInNewTab()
                    ->visible(fn (MoneyTransferInvoice $record) =>
                        $record->status === 'completed' && ! empty($record->transaction_slip)
                    ),

                // Action 3: View popup with professional dark design
                Action::make('view_details')
                    ->label(__('message.View'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('')   // Empty — custom header is inside the blade itself
                    ->modalWidth('lg')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('✕ ' . __('message.Close'))
                    ->modalContent(fn ($record) =>
                        view('filament.modals.transfer-in-details', compact('record'))
                    ),
            ])
            ->headerActions([
                Action::make('export_excel')
                    ->label(__('message.export_excel'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        Notification::make()
                            ->title(__('message.export_success'))
                            ->body(__('message.excel_downloading'))
                            ->success()
                            ->send();

                        return Excel::download(
                            new TransferInReportExport(),
                            'transfer-in-report-' . now()->format('Y-m-d-His') . '.xlsx'
                        );
                    }),
            ])
            ->bulkActions([])
            ->striped()
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferInReports::route('/'),
        ];
    }
}