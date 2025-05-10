<?php

namespace App\Filament\Pages\System;

use App\Services\LogViewer;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Logs extends Page
{
    use HasPageShield;

    protected static string $view = 'filament.pages.system.logs';

    public ?string $logFile = null;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_logs');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_system_manager');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_logs');
    }

    public static function canAccess(): bool
    {
        return userHasPermission('page_Logs');
    }
    /**
     * @throws FileNotFoundException
     */
    public function getLogs(): Collection
    {
        if (!$this->logFile) {
            return collect([]);
        }

        $logs = LogViewer::getAllForFile($this->logFile);

        return collect($logs);
    }

    /**
     * @throws Exception
     */
    public function download(): BinaryFileResponse
    {
        return response()->download(LogViewer::pathToLogFile($this->logFile));
    }

    /**
     * @throws Exception
     */
    public function delete(): bool
    {
        if (File::delete(LogViewer::pathToLogFile($this->logFile))) {
            $this->logFile = null;

            return true;
        }

        abort(404, __('messages.t_ap_no_such_file'));
    }

    protected function getForms(): array
    {
        return [
            'search' => $this->makeForm()
                ->schema($this->getFormSchema()),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('logFile')
                ->searchable()
                ->reactive()
                ->hiddenLabel()
                ->placeholder(__('messages.t_ap_select_or_search_log_file'))  // Multi-language placeholder
                ->options(fn() => $this->getFileNames($this->getFinder())->take(5))
                ->getSearchResultsUsing(fn(string $query) => $this->getFileNames($this->getFinder()->name("*{$query}*")))
                ->label(__('messages.t_ap_log_file'))  // Multi-language label
        ];
    }

    protected function getFileNames($files): Collection
    {
        return collect($files)->mapWithKeys(function (SplFileInfo $file) {
            return [$file->getRealPath() => $file->getRealPath()];
        });
    }


    protected function getFinder(): Finder
    {
        return Finder::create()
            ->ignoreDotFiles(true)
            ->ignoreUnreadableDirs()
            ->files()
            ->in(storage_path('logs'));
    }

}
