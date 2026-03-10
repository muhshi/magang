<div wire:poll.5s>
    <div class="admin-chat-container">
        {{-- Sidebar: Conversation List --}}
        <div class="admin-chat-sidebar">
            <div class="admin-chat-sidebar-header">
                <h3>Pesan Masuk</h3>
            </div>

            <div class="admin-chat-conversation-list">
                @forelse($this->conversations as $conversation)
                    <button
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="admin-chat-conversation-item {{ $selectedConversationId === $conversation->id ? 'active' : '' }}"
                    >
                        <div class="admin-chat-conv-avatar">
                            {{ strtoupper(substr($conversation->user->name, 0, 1)) }}
                        </div>
                        <div class="admin-chat-conv-info">
                            <div class="admin-chat-conv-name-row">
                                <span class="admin-chat-conv-name">{{ $conversation->user->name }}</span>
                                @if(!$conversation->is_read_by_admin)
                                    <span class="admin-chat-unread-dot"></span>
                                @endif
                            </div>
                            <p class="admin-chat-conv-preview">
                                {{ $conversation->latestMessage?->body ? Str::limit($conversation->latestMessage->body, 40) : ($conversation->latestMessage?->attachment ? '📎 File' : 'Belum ada pesan') }}
                            </p>
                            <span class="admin-chat-conv-time">
                                {{ $conversation->last_message_at?->diffForHumans() ?? '' }}
                            </span>
                        </div>
                    </button>
                @empty
                    <div class="admin-chat-empty-sidebar">
                        <p>Belum ada percakapan</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Main: Chat Detail --}}
        <div class="admin-chat-main">
            @if($selectedConversationId && $this->selectedConversation)
                {{-- Chat Header --}}
                <div class="admin-chat-main-header">
                    <div class="admin-chat-main-avatar">
                        {{ strtoupper(substr($this->selectedConversation->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4>{{ $this->selectedConversation->user->name }}</h4>
                        <small>{{ $this->selectedConversation->user->email }}</small>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="admin-chat-messages" id="admin-chat-messages-container">
                    @foreach($this->messages as $message)
                        <div class="chat-bubble {{ $message->sender_id === auth()->id() ? 'chat-bubble-admin-sent' : 'chat-bubble-admin-received' }}">
                            <div class="chat-bubble-sender-label">
                                {{ $message->sender_id === auth()->id() ? 'Anda' : $message->sender->name }}
                            </div>
                            @if($message->attachment)
                                <div class="chat-attachment">
                                    @if(in_array(pathinfo($message->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ asset('storage/' . $message->attachment) }}" alt="attachment" class="chat-attachment-img" onclick="window.open(this.src, '_blank')">
                                    @else
                                        <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank" class="chat-attachment-file-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/></svg>
                                            {{ $message->attachment_name ?? 'File' }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                            @if($message->body)
                                <p class="chat-bubble-text">{{ $message->body }}</p>
                            @endif
                            <span class="chat-bubble-time">{{ $message->created_at->format('d M H:i') }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Attachment Preview --}}
                @if($attachment)
                    <div class="admin-chat-attachment-preview">
                        <span>📎 {{ $attachment->getClientOriginalName() }}</span>
                        <button wire:click="removeAttachment" class="admin-chat-attachment-remove">&times;</button>
                    </div>
                @endif

                {{-- Reply Input --}}
                <form wire:submit="sendReply" class="admin-chat-reply-area">
                    <label for="admin-chat-file" class="admin-chat-attach-btn" title="Lampirkan file">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                        </svg>
                        <input type="file" wire:model="attachment" id="admin-chat-file" style="display:none;">
                    </label>
                    <input
                        type="text"
                        wire:model="replyMessage"
                        placeholder="Ketik balasan..."
                        class="admin-chat-input"
                        autocomplete="off"
                    >
                    <button type="submit" class="admin-chat-send-btn" title="Kirim">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                        </svg>
                    </button>
                </form>
            @else
                <div class="admin-chat-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                         style="opacity: 0.2;">
                        <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/>
                    </svg>
                    <h3>Pilih percakapan</h3>
                    <p>Pilih salah satu percakapan dari daftar di sebelah kiri</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        .admin-chat-container {
            display: flex;
            height: calc(100vh - 200px);
            min-height: 500px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            font-family: 'Inter', system-ui, sans-serif;
        }

        /* Sidebar */
        .admin-chat-sidebar {
            width: 320px;
            min-width: 280px;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }
        .admin-chat-sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
        }
        .admin-chat-sidebar-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
        }

        .admin-chat-conversation-list {
            flex: 1;
            overflow-y: auto;
        }
        .admin-chat-conversation-item {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border: none;
            background: transparent;
            cursor: pointer;
            text-align: left;
            transition: background 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }
        .admin-chat-conversation-item:hover { background: #e2e8f0; }
        .admin-chat-conversation-item.active { background: #dbeafe; }
        .admin-chat-conv-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }
        .admin-chat-conv-info { flex: 1; min-width: 0; }
        .admin-chat-conv-name-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-chat-conv-name {
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
        }
        .admin-chat-unread-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #3b82f6;
            flex-shrink: 0;
        }
        .admin-chat-conv-preview {
            margin: 2px 0 0;
            font-size: 13px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .admin-chat-conv-time {
            font-size: 11px;
            color: #94a3b8;
        }
        .admin-chat-empty-sidebar {
            padding: 40px 20px;
            text-align: center;
            color: #94a3b8;
        }

        /* Main Area */
        .admin-chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }
        .admin-chat-main-header {
            padding: 16px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 14px;
            background: white;
        }
        .admin-chat-main-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
        }
        .admin-chat-main-header h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }
        .admin-chat-main-header small {
            color: #94a3b8;
            font-size: 12px;
        }
        .admin-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f8fafc;
        }
        .chat-bubble {
            max-width: 70%;
            padding: 10px 14px;
            border-radius: 16px;
            word-wrap: break-word;
        }
        .chat-bubble-admin-sent {
            align-self: flex-end;
            background: #3b82f6;
            color: white;
            border-bottom-right-radius: 4px;
        }
        .chat-bubble-admin-received {
            align-self: flex-start;
            background: white;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .chat-bubble-sender-label {
            font-size: 11px;
            font-weight: 600;
            opacity: 0.7;
            margin-bottom: 4px;
        }
        .chat-bubble-text {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .chat-bubble-time {
            font-size: 11px;
            opacity: 0.6;
            display: block;
            margin-top: 4px;
            text-align: right;
        }
        .chat-attachment { margin-bottom: 6px; }
        .chat-attachment-img {
            max-width: 240px;
            border-radius: 8px;
            cursor: pointer;
        }
        .chat-attachment-file-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: rgba(0,0,0,0.05);
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
            font-size: 13px;
        }
        .admin-chat-attachment-preview {
            padding: 8px 24px;
            background: #e0f2fe;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: #1e40af;
        }
        .admin-chat-attachment-remove {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #ef4444;
        }
        .admin-chat-reply-area {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 24px;
            border-top: 1px solid #e2e8f0;
            background: white;
        }
        .admin-chat-attach-btn {
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .admin-chat-attach-btn:hover { color: #3b82f6; }
        .admin-chat-input {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 10px 18px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            background: #f8fafc;
            color: #1e293b;
        }
        .admin-chat-input:focus { border-color: #3b82f6; background: white; }
        .admin-chat-send-btn {
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .admin-chat-send-btn:hover { background: #2563eb; }
        .admin-chat-placeholder {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            text-align: center;
            gap: 8px;
        }
        .admin-chat-placeholder h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #64748b;
        }
        .admin-chat-placeholder p {
            margin: 0;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .admin-chat-container { flex-direction: column; height: auto; }
            .admin-chat-sidebar { width: 100%; min-width: 100%; max-height: 250px; border-right: none; border-bottom: 1px solid #e2e8f0; }
            .admin-chat-main { min-height: 400px; }
        }

        /* ========== DARK MODE (Filament .dark class) ========== */
        .dark .admin-chat-container {
            background: #1e293b;
            border-color: #334155;
        }
        .dark .admin-chat-sidebar {
            background: #0f172a;
            border-right-color: #334155;
        }
        .dark .admin-chat-sidebar-header {
            background: #1e293b;
            border-bottom-color: #334155;
        }
        .dark .admin-chat-sidebar-header h3 {
            color: #e2e8f0;
        }
        .dark .admin-chat-conversation-item {
            border-bottom-color: #1e293b;
        }
        .dark .admin-chat-conversation-item:hover {
            background: #334155;
        }
        .dark .admin-chat-conversation-item.active {
            background: #1e3a5f;
        }
        .dark .admin-chat-conv-name {
            color: #e2e8f0;
        }
        .dark .admin-chat-conv-preview {
            color: #94a3b8;
        }
        .dark .admin-chat-conv-time {
            color: #64748b;
        }
        .dark .admin-chat-empty-sidebar {
            color: #64748b;
        }
        .dark .admin-chat-main {
            background: #1e293b;
        }
        .dark .admin-chat-main-header {
            background: #1e293b;
            border-bottom-color: #334155;
        }
        .dark .admin-chat-main-header h4 {
            color: #e2e8f0;
        }
        .dark .admin-chat-main-header small {
            color: #64748b;
        }
        .dark .admin-chat-messages {
            background: #0f172a;
        }
        .dark .chat-bubble-admin-received {
            background: #334155;
            color: #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        .dark .chat-attachment-file-link {
            background: rgba(255,255,255,0.1);
        }
        .dark .admin-chat-attachment-preview {
            background: #1e3a5f;
            color: #93c5fd;
        }
        .dark .admin-chat-reply-area {
            background: #1e293b;
            border-top-color: #334155;
        }
        .dark .admin-chat-attach-btn {
            color: #94a3b8;
        }
        .dark .admin-chat-input {
            background: #0f172a;
            border-color: #334155;
            color: #e2e8f0;
        }
        .dark .admin-chat-input:focus {
            border-color: #3b82f6;
            background: #1e293b;
        }
        .dark .admin-chat-placeholder {
            color: #64748b;
        }
        .dark .admin-chat-placeholder h3 {
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .dark .admin-chat-sidebar { border-bottom-color: #334155; }
        }
    </style>

    @script
    <script>
        $wire.on('reply-sent', () => {
            setTimeout(() => {
                const container = document.getElementById('admin-chat-messages-container');
                if (container) container.scrollTop = container.scrollHeight;
            }, 100);
        });
    </script>
    @endscript
</div>
