<?php

namespace App\Filament\Imports;

use App\Models\CategoryIndustry;
use App\Models\ProspectLead;
use App\Models\StatusLead;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;

class ProspectLeadImporter extends Importer
{
    protected static ?string $model = ProspectLead::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('company_name')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('category_industries_id')
                ->rules(['max:255']),
            ImportColumn::make('industry_type')
                ->rules(['max:255']),
            ImportColumn::make('phone')
                ->rules(['max:255']),
            ImportColumn::make('email')
                ->rules(['max:255']),
            ImportColumn::make('address')
                ->rules(['max:255']),
            ImportColumn::make('pic')
                ->rules(['max:255']),
            ImportColumn::make('status_leads_id')
                ->requiredMapping(),
        ];
    }

    public function resolveRecord(): ?ProspectLead
    {
        // return ProspectLead::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new ProspectLead();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your prospect lead import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function beforeValidate(): void
    {
        $status_leads_id = StatusLead::query()->where('status', $this->data['status_leads_id'])->first()?->id;
        $this->data['status_leads_id'] = $status_leads_id;
        $category_industries_id = CategoryIndustry::query()->where('category', $this->data['category_industries_id'])->first()?->id;
        $this->data['category_industries_id'] = $category_industries_id;

        // if (auth::check()) {  // Memastikan pengguna login
        //     $this->data['followup_by'] = auth::user()->name; // Atau auth()->user()->id jika Anda menggunakan ID
        // } else {
        //     // Jika pengguna tidak terautentikasi, Anda bisa menangani ini dengan mengisi nilai default atau melemparkan error
        //     $this->data['followup_by'] = 'Unknown'; // Default jika pengguna tidak terautentikasi
        // }
    }
}
