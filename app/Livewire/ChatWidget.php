<?php

namespace App\Livewire;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ChatWidget extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;
    public string $newMessage = '';
    public $attachment = null;
    public ?int $conversationId = null;
    public int $unreadCount = 0;

    public function mount()
    {
        if (Auth::check()) {
            $conversation = Auth::user()->chatConversation;
            if ($conversation) {
                $this->conversationId = $conversation->id;
                $this->unreadCount = $conversation->is_read_by_user ? 0 : 1;
            }
        }
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;

        if ($this->isOpen && $this->conversationId) {
            // Mark as read by user
            ChatConversation::where('id', $this->conversationId)
                ->update(['is_read_by_user' => true]);
            $this->unreadCount = 0;
        }
    }

    public function sendMessage()
    {
        if (empty($this->newMessage) && !$this->attachment) {
            return;
        }

        $user = Auth::user();

        // Create conversation if not exists
        if (!$this->conversationId) {
            $conversation = ChatConversation::create([
                'user_id' => $user->id,
                'last_message_at' => now(),
                'is_read_by_admin' => false,
                'is_read_by_user' => true,
            ]);
            $this->conversationId = $conversation->id;
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($this->attachment) {
            $attachmentName = $this->attachment->getClientOriginalName();
            $attachmentPath = $this->attachment->store('chat-attachments', 'public');
        }

        ChatMessage::create([
            'conversation_id' => $this->conversationId,
            'sender_id' => $user->id,
            'body' => $this->newMessage ?: null,
            'attachment' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'is_read' => false,
        ]);

        // Update conversation
        ChatConversation::where('id', $this->conversationId)->update([
            'last_message_at' => now(),
            'is_read_by_admin' => false,
            'is_read_by_user' => true,
        ]);

        $this->reset(['newMessage', 'attachment']);
        $this->dispatch('message-sent');
    }

    public function removeAttachment()
    {
        $this->reset('attachment');
    }

    public function pollMessages()
    {
        if ($this->conversationId) {
            $conversation = ChatConversation::find($this->conversationId);
            if ($conversation && !$conversation->is_read_by_user) {
                $this->unreadCount = 1;
                if ($this->isOpen) {
                    $conversation->update(['is_read_by_user' => true]);
                    $this->unreadCount = 0;
                }
            }
        }
    }

    public function getMessagesProperty()
    {
        if (!$this->conversationId) {
            return collect();
        }

        return ChatMessage::where('conversation_id', $this->conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.chat-widget');
    }
}
