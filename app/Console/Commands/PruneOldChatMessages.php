<?php

namespace App\Console\Commands;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneOldChatMessages extends Command
{
    protected $signature = 'chat:prune {--days=60 : Jumlah hari retensi pesan}';

    protected $description = 'Hapus pesan chat yang lebih lama dari batas retensi';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $this->info("Menghapus pesan chat sebelum {$cutoff->toDateString()}...");

        // Get messages to delete (for attachment cleanup)
        $oldMessages = ChatMessage::where('created_at', '<', $cutoff)->get();

        $attachmentCount = 0;
        foreach ($oldMessages as $message) {
            if ($message->attachment && Storage::disk('public')->exists($message->attachment)) {
                Storage::disk('public')->delete($message->attachment);
                $attachmentCount++;
            }
        }

        // Delete old messages
        $deletedCount = ChatMessage::where('created_at', '<', $cutoff)->delete();

        // Delete conversations without messages
        $emptyConversations = ChatConversation::whereDoesntHave('messages')->delete();

        $this->info("Selesai: {$deletedCount} pesan dihapus, {$attachmentCount} file dihapus, {$emptyConversations} percakapan kosong dihapus.");

        return self::SUCCESS;
    }
}
