<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ad;
use Tiptap\Editor;
use Illuminate\Support\Str;

class ConvertAdDescriptions extends Command
{
    protected $signature = 'convert:descriptions';
    protected $description = 'Convert existing ad descriptions to Tiptap JSON format';

    public function handle()
    {
        $this->info('Converting descriptions to Tiptap format...');

        $ads = Ad::whereNotNull('description')->get();
        $editor = new Editor();

        foreach ($ads as $ad) {
            $html = Str::markdown($ad->description); // Convert Markdown to HTML
            $tiptapJson = $editor->setContent($html)->getDocument(); // Convert to Tiptap JSON

            $ad->update(['description_tiptap' => $tiptapJson]); // Save to DB
            $this->info("Updated Ad ID: {$ad->id}");
        }

        $this->info('Conversion completed successfully.');
    }
}

