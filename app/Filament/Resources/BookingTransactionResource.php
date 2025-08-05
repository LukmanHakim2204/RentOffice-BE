<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Twilio\Rest\Client;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\BookingTransaction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('booking_trx_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('duration')
                    ->required()
                    ->numeric()
                    ->prefix('Days'),
                Forms\Components\DatePicker::make('started_at')
                    ->required(),
                Forms\Components\DatePicker::make('ended_at')
                    ->required(),
                Forms\Components\Select::make('is_paid')
                    ->options([
                        true => 'Paid',
                        false => 'Not Paid',
                    ]),
                Forms\Components\Select::make('office_space_id')
                    ->relationship('officeSpace', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('booking_trx_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('officeSpace.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ended_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-s-x-circle')
                    ->label('Sudah Bayar?'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->action(function (BookingTransaction $record) {
                        $record->is_paid = true;
                        $record->save();


                        Notification::make()
                            ->title('Approved')
                            ->success()
                            ->body('The Booking Transaction has been approved.')
                            ->send();

                        $ssid = getenv('TWILIO_ACCOUNT_SID');
                        $token = getenv('TWILIO_AUTH_TOKEN');
                        $twilio = new Client($ssid, $token);

                        $messageBody = "Hi {$record->name}, pesanan anda dengan kode {$record->booking_trx_id} sudah terbayar penuh. \n \n";
                        $messageBody .=  "Silahkan datang kepada lokasi Kantor {$record->officeSpace->name} untuk memulai menggunakan ruangan tersebut. \n \n";
                        $messageBody .=  "Jika memiliki kendala silahkan menghubungi CS Lukman Hakim, Terima Kasih.";


                        $phoneNumber = $record->phone_number;

                        // Jika nomor dimulai dengan 08, ganti dengan +628
                        if (substr($phoneNumber, 0, 2) == '08') {
                            $phoneNumber = '+628' . substr($phoneNumber, 2);
                        }
                        // Jika sudah ada +, gunakan langsung
                        elseif (substr($phoneNumber, 0, 1) != '+') {
                            $phoneNumber = '+62' . ltrim($phoneNumber, '0');
                        }
                        $message = $twilio->messages
                            ->create(
                                "whatsapp:{$phoneNumber}", // to
                                array(
                                    "from" => "whatsapp:+14155238886",
                                    "body" => $messageBody
                                )
                            );
                    })->icon('heroicon-s-check-circle')
                    ->color('success')
                    ->visible(function (BookingTransaction $record) {
                        return $record->is_paid == false;
                    }),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBookingTransactions::route('/'),
            'create' => Pages\CreateBookingTransaction::route('/create'),
            'edit' => Pages\EditBookingTransaction::route('/{record}/edit'),
        ];
    }
}
