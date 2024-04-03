<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Illuminate\Support\Facades\DB;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Select::make('category_id')
                    ->options(Category::pluck('name', 'id'))
                    ->label('Category')
                    ->required(),

                Forms\Components\Select::make('sub_category_id')
                    ->options(DB::table('sub_categories')->pluck('name', 'id'))
                    ->label('Subcategory')
                    ->required(),
                Forms\Components\FileUpload::make('image'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('category_id')
                    ->getStateUsing(function ($record) {
                        $categoryId = $record->category_id;
                        if ($categoryId) {
                            return DB::table('categories')->where('id', $categoryId)->value('name');
                        }
                        return '';
                    })
                    ->label('Category'),
                Tables\Columns\TextColumn::make('sub_category_id')
                    ->getStateUsing(function ($record) {
                        $subCategoryId = $record->sub_category_id;
                        if ($subCategoryId) {
                            return DB::table('sub_categories')->where('id', $subCategoryId)->value('name');
                        }
                        return '';
                    })
                    ->label('Sub Category'),
                    Tables\Columns\ImageColumn::make('image')
                    ->extraImgAttributes([
                        'class' => 'round-image',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
