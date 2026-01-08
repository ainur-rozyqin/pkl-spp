<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Department;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->default(fn() => 'TRX'. mt_rand(10000, 99999)),
                Select::make('user_id')
                    ->required()
                    ->relationship('users','name'),
                TextInput::make('payment_status')
                    ->readOnly()
                    ->default('pending'),
                Fieldset::make('Department')
                    ->schema([
                       Select::make('department_id')
                        ->required()
                        ->label('Department Name & Semester')
                        ->options(Department::query()->get()->mapWithKeys(function ($department)
                        {
                            return [
                                $department->id => $department->name . ' - Semester ' . $department->semester
                            ];
                        })->toArray())
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($department = Department::find($state))
                            {
                                $set('department_cost', $department->cost);
                            } else {
                                $set('department_cost', null);
                            }
                        }),
                        TextInput::make('department_cost')
                            ->label('Cost')
                            ->disabled(),
                    ]),
            ]);
    }
}
