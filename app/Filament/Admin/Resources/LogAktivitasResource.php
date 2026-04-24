<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LogAktivitasResource\Pages;
use App\Models\LogAktivitas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LogAktivitasResource extends Resource
{
    protected static ?string $model = LogAktivitas::class;

    // PERBAIKAN: Di v3 hanya boleh ?string
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $modelLabel = 'Log Aktivitas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id'),
                Forms\Components\TextInput::make('aktivitas'),
                Forms\Components\DateTimePicker::make('created_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('aktivitas')->label('Aktivitas'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Waktu'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogAktivitas::route('/'),
        ];
    }
}