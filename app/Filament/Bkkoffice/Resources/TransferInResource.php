<?php

namespace App\Filament\Bkkoffice\Resources;

use App\Filament\Bkkoffice\Resources\TransferInResource\Pages;
use App\Models\MoneyTransferInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class TransferInResource extends Resource
{
    protected static ?string $model = MoneyTransferInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-left';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('message.Transfer-IN Requests');
    }

    public static function getModelLabel(): string
    {
        return __('message.Transfer-IN Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.Transfer-IN Requests');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('message.Transfers');
    }

    // ✅ Sidebar badge — pending Transfer-IN today
    public static function getNavigationBadge(): ?string
    {
        $count = MoneyTransferInvoice::whereIn('status', [
                'pending_bkk_approval',
                'accepted_bkk'
            ])
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('message.Pending Transfer-IN approvals');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('message.Transfer Details'))
                ->schema([
                    Forms\Components\TextInput::make('invoice_number')
                        ->label(__('message.Invoice Number'))->disabled(),
                    Forms\Components\TextInput::make('customer_name')
                        ->label(__('message.Customer Name'))->disabled(),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('message.Phone'))->disabled(),
                    Forms\Components\TextInput::make('bank_name')
                        ->label(__('message.Bank Name'))->disabled(),
                    Forms\Components\TextInput::make('acc_name')
                        ->label(__('message.Account Name'))->disabled(),
                    Forms\Components\TextInput::make('acc_number')
                        ->label(__('message.Account Number'))->disabled(),
                    Forms\Components\TextInput::make('currency')
                        ->label(__('message.Currency'))->disabled(),
                    Forms\Components\TextInput::make('entered_amount')
                        ->label(__('message.Amount'))->disabled(),
                    Forms\Components\TextInput::make('trf_fee')
                        ->label(__('message.Transfer Fee'))->disabled(),
                    Forms\Components\TextInput::make('net_amount')
                        ->label(__('message.Net Amount'))->disabled(),
                    Forms\Components\Select::make('status')
                        ->label(__('message.Status'))
                        ->options([
                            'pending_bkk_approval' => __('message.Pending BKK Approval'),
                            'accepted_bkk'         => __('message.Accepted'),
                            'completed'            => __('message.Completed'),
                            'Rejected'             => __('message.Rejected'),
                            'cancelled'            => __('message.Cancelled'),
                        ]),
                    Forms\Components\Textarea::make('reject_reason')
                        ->label(__('message.Reason for Rejection'))
                        ->disabled()
                        ->rows(2)
                        ->columnSpanFull()
                        ->visible(fn ($record) => $record?->status === 'Rejected'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                MoneyTransferInvoice::query()
                    ->where('transfer_type', 'Transfer-IN')
                    ->whereDate('created_at', today())
                    ->orderBy('id', 'desc')
            )
            ->columns([
                TextColumn::make('id')
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
                    ->label(__('message.Invoice #'))
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

               TextColumn::make('customer_name')
                    ->label(__('message.Customer name'))
                    ->html()
                    ->searchable()
                    ->getStateUsing(fn ($record) =>
                        '<strong>' . Str::ucfirst($record->customer_name) . '</strong><br>' .
                        Str::ucfirst($record->phone)
                    ),
                // ✅ Combined Bank Details column
                TextColumn::make('bank_name')
                    ->label(__('message.Bank Details'))
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->bank_name ?? '—') . "\n" . ($record->acc_number ?? '')
                    )
                    ->html()
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return '<div style="line-height:1.4">
                            <span style="font-weight:700">' . e($record->bank_name ?? '—') . '</span><br>
                            <span style="font-size:12px;color:#94a3b8">' . e($record->acc_number ?? '') . '</span><br>
                            <span style="font-size:11px;color:#64748b">' . e($record->acc_name ?? '') . '</span>
                        </div>';
                    })->copyable(),

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

                TextColumn::make('transaction_slip')
                    ->label(__('message.Slip'))
                    ->formatStateUsing(fn ($state) => $state
                        ? '✅ ' . __('message.Uploaded')
                        : '—')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

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
                    }),
                    TextColumn::make('reject_reason')
                        ->label(__('message.Reject Reason'))
                        ->limit(30)->placeholder('—')
                        ->color('danger')
                        ->searchable()
                        ->tooltip(fn ($record) => $record?->reject_reason),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('message.Status'))
                    ->options([
                        'pending_bkk_approval' => '⏳ ' . __('message.Pending'),
                        'accepted_bkk'         => '✅ ' . __('message.Accepted'),
                        'completed'            => '✔ '  . __('message.Completed'),
                        'Rejected'             => '❌ ' . __('message.Rejected'),
                    ])
                    ->placeholder(__('message.All Statuses')),
            ])
            ->actions([
                // ✅ View Invoice
                Action::make('view_invoice')
                    ->label(__('message.View Invoice'))
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (MoneyTransferInvoice $record) => $record->invoice_url)
                    ->openUrlInNewTab()
                    ->visible(fn (MoneyTransferInvoice $record) => ! empty($record->invoice_url)),

                // ✅ Accept → opens upload slip popup
                Action::make('accept_and_upload')
                    ->label(__('message.Accept & Upload'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->modalHeading(__('message.Accept & Upload Transaction Slip'))
                    ->modalDescription(__('message.Verify the bank transfer and upload the receipt to complete this Transfer-IN.'))
                    ->modalSubmitActionLabel(__('message.Upload & Complete'))
                    ->modalCancelActionLabel(__('message.Cancel'))
                    ->modalWidth('md')
                    ->form([
                        Forms\Components\Placeholder::make('transfer_info')
                            ->label(__('message.Transfer Details'))
                            ->content(fn (MoneyTransferInvoice $record) =>
                                "{$record->invoice_number} | {$record->bank_name} | {$record->acc_number} | {$record->currency} " . number_format($record->net_amount, 2)
                            ),

                        Forms\Components\FileUpload::make('transaction_slip')
                            ->label(__('message.Transaction Slip'))
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('transaction-slips-in')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'])
                            ->maxSize(5120)
                            ->required()
                            ->helperText(__('message.Drag & drop or click to upload. Max 5MB.'))
                            ->placeholder(__('message.Drop slip here or click to browse')),
                    ])
                    ->action(function (MoneyTransferInvoice $record, array $data) {
                        $record->update([
                            'status'           => 'completed',
                            'transaction_slip' => $data['transaction_slip'],
                        ]);

                        Notification::make()
                            ->title('✅ ' . __('message.Transfer-IN Completed!'))
                            ->body(__('message.Invoice') . " {$record->invoice_number} " . __('message.marked as completed. Slip saved.'))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (MoneyTransferInvoice $record) => $record->status === 'pending_bkk_approval'),

                // ✅ Reject with dropdown + optional textarea
                Action::make('reject')
                    ->label(__('message.Reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading(__('message.Reject Transfer-IN Request'))
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
                    ->visible(fn (MoneyTransferInvoice $record) => $record->status === 'pending_bkk_approval'),

                // ✅ Upload slip for accepted records
                Action::make('upload_slip')
                    ->label(__('message.Upload Slip'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->modalHeading(__('message.Upload Transaction Slip'))
                    ->modalDescription(__('message.Upload the bank transfer receipt after processing the payment.'))
                    ->modalSubmitActionLabel(__('message.Upload & Complete'))
                    ->modalCancelActionLabel(__('message.Cancel'))
                    ->modalWidth('md')
                    ->form([
                        Forms\Components\Placeholder::make('invoice_info')
                            ->label(__('message.Invoice'))
                            ->content(fn (MoneyTransferInvoice $record) =>
                                "{$record->invoice_number} — {$record->bank_name} | {$record->acc_number} | {$record->currency} " . number_format($record->net_amount, 2)
                            ),
                        Forms\Components\FileUpload::make('transaction_slip')
                            ->label(__('message.Transaction Slip'))
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('transaction-slips-in')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'])
                            ->maxSize(5120)
                            ->required()
                            ->helperText(__('message.Drag & drop or click to upload. Max 5MB.'))
                            ->placeholder(__('message.Drop slip here or click to browse')),
                    ])
                    ->action(function (MoneyTransferInvoice $record, array $data) {
                        $record->update([
                            'transaction_slip' => $data['transaction_slip'],
                            'status'           => 'completed',
                        ]);

                        Notification::make()
                            ->title('✅ ' . __('message.Completed!'))
                            ->body(__('message.Invoice') . " {$record->invoice_number} " . __('message.marked as completed. Slip saved.'))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (MoneyTransferInvoice $record) => $record->status === 'accepted_bkk'),

                // ✅ View uploaded slip
                Action::make('view_slip')
                    ->label(__('message.View Slip'))
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->url(fn (MoneyTransferInvoice $record) => asset('storage/' . $record->transaction_slip))
                    ->openUrlInNewTab()
                    ->visible(fn (MoneyTransferInvoice $record) =>
                        $record->status === 'completed' && ! empty($record->transaction_slip)
                    ),
            ])
            ->bulkActions([])
            ->poll('8s')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferIn::route('/'),
        ];
    }
}