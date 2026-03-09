<?php

namespace App\Livewire;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminChatInbox extends Component
{
    use WithFileUploads;

    public ?int $selectedConversationId = null;
    public string $replyMessage = '';
    public $attachment = null;

    public function selectConversation(int $id)
    {
        $this->selectedConversationId = $id;
        $this->reset(['replyMessage', 'attachment']);

        // Mark as read by admin
        ChatConversation::where('id', $id)->update(['is_read_by_admin' => true]);

        // Mark all messages as read
        ChatMessage::where('conversation_id', $id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function sendReply()
    {
        if ((empty($this->replyMessage) && !$this->attachment) || !$this->selectedConversationId) {
            return;
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($this->attachment) {
            $attachmentName = $this->attachment->getClientOriginalName();
            $attachmentPath = $this->attachment->store('chat-attachments', 'public');
        }

        ChatMessage::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => Auth::id(),
            'body' => $this->replyMessage ?: null,
            'attachment' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'is_read' => false,
        ]);

        ChatConversation::where('id', $this->selectedConversationId)->update([
            'last_message_at' => now(),
            'is_read_by_admin' => true,
            'is_read_by_user' => false,
        ]);

        $this->reset(['replyMessage', 'attachment']);
        $this->dispatch('reply-sent');
    }

    public function removeAttachment()
    {
        $this->reset('attachment');
    }

    public function getConversationsProperty()
    {
        return ChatConversation::with(['user', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->get();
    }

    public function getMessagesProperty()
    {
        if (!$this->selectedConversationId) {
            return collect();
        }

        return ChatMessage::where('conversation_id', $this->selectedConversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getSelectedConversationProperty()
    {
        if (!$this->selectedConversationId) {
            return null;
        }

        return ChatConversation::with('user')->find($this->selectedConversationId);
    }

    public function render()
    {
        return view('livewire.admin-chat-inbox');
    }
}
