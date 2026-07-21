<?php

namespace App\Console\Commands;

use App\Models\ProkerDocument;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateStorageStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superposko:migrate-storage {--dry-run : Simulate migration without moving files or updating database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing MinIO/S3 files to the new client/{group_number}/{category}/ directory structure.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $diskName = env('FILESYSTEM_DISK', 's3');
        $disk = Storage::disk($diskName);

        $this->info("Starting storage migration on disk: [{$diskName}]".($isDryRun ? ' (DRY RUN MODE)' : ''));

        // 1. Migrate Proker Documents
        $documents = ProkerDocument::all();
        $this->info("Found {$documents->count()} proker documents to check.");

        $migratedDocs = 0;
        foreach ($documents as $doc) {
            $hostUser = User::find($doc->host_id);
            $groupSlug = Str::slug($hostUser?->group_number ?: "kelompok-{$doc->host_id}", '_');
            $newPathPattern = "client/{$groupSlug}/documents/";

            if (! Str::startsWith($doc->file_path, $newPathPattern)) {
                $fileName = basename($doc->file_path);
                $targetPath = "client/{$groupSlug}/documents/{$fileName}";

                $this->line("Migrating Document ID {$doc->id}: [{$doc->file_path}] -> [{$targetPath}]");

                if (! $isDryRun) {
                    if ($disk->exists($doc->file_path)) {
                        $disk->copy($doc->file_path, $targetPath);
                        if ($disk->exists($targetPath)) {
                            $disk->delete($doc->file_path);
                            $doc->update(['file_path' => $targetPath]);
                            $migratedDocs++;
                        } else {
                            $this->error("Failed to copy {$doc->file_path} to {$targetPath}");
                        }
                    } else {
                        // Physical file missing in old location, update path directly
                        $doc->update(['file_path' => $targetPath]);
                        $migratedDocs++;
                    }
                } else {
                    $migratedDocs++;
                }
            }
        }

        $this->info("Successfully migrated {$migratedDocs} proker documents.");
        $this->info("Storage migration completed cleanly!");

        return Command::SUCCESS;
    }
}
