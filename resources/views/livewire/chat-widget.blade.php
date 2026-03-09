<div>
    {{-- Floating Chat Button --}}
    <button
        wire:click="toggleChat"
        class="chat-float-btn"
        id="chat-float-btn"
        title="Chat dengan Admin"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/>
        </svg>
        @if($unreadCount > 0)
            <span class="chat-unread-badge">{{ $unreadCount }}</span>
        @endif
    </button>

    {{-- Chat Panel --}}
    @if($isOpen)
        <div class="chat-panel" wire:poll.5s="pollMessages">
            {{-- Header --}}
            <div class="chat-panel-header">
                <div class="chat-panel-header-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/>
                    </svg>
                    <span>Chat dengan Admin</span>
                </div>
                <button wire:click="toggleChat" class="chat-panel-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Messages Area --}}
            <div class="chat-messages" id="chat-messages-container">
                @if($this->messages->isEmpty())
                    <div class="chat-empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                             style="opacity: 0.3; margin-bottom: 12px;">
                            <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/>
                        </svg>
                        <p>Belum ada pesan</p>
                        <small>Mulai chat dengan admin</small>
                    </div>
                @else
                    @foreach($this->messages as $message)
                        <div class="chat-bubble {{ $message->sender_id === auth()->id() ? 'chat-bubble-sent' : 'chat-bubble-received' }}">
                            @if($message->attachment)
                                <div class="chat-attachment">
                                    @if(in_array(pathinfo($message->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ asset('storage/' . $message->attachment) }}" alt="attachment" class="chat-attachment-img" onclick="window.open(this.src, '_blank')">
                                    @else
                                        <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank" class="chat-attachment-file">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/></svg>
                                            {{ $message->attachment_name ?? 'File' }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                            @if($message->body)
                                <p class="chat-bubble-text">{{ $message->body }}</p>
                            @endif
                            <span class="chat-bubble-time">{{ $message->created_at->format('H:i') }}</span>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Attachment Preview --}}
            @if($attachment)
                <div class="chat-attachment-preview">
                    <span>📎 {{ $attachment->getClientOriginalName() }}</span>
                    <button wire:click="removeAttachment" class="chat-attachment-remove">&times;</button>
                </div>
            @endif

            {{-- Input Area --}}
            <form wire:submit="sendMessage" class="chat-input-area">
                <label for="chat-file-input" class="chat-attach-btn" title="Lampirkan file">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                    </svg>
                    <input type="file" wire:model="attachment" id="chat-file-input" class="chat-file-hidden">
                </label>
                <input
                    type="text"
                    wire:model="newMessage"
                    placeholder="Ketik pesan..."
                    class="chat-input"
                    autocomplete="off"
                >
                <button type="submit" class="chat-send-btn" title="Kirim">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                    </svg>
                </button>
            </form>
        </div>
    @endif

    <style>
        .chat-float-btn {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 9999;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
        }
        .chat-float-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
        }
        .chat-unread-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }
        .chat-panel {
            position: fixed;
            bottom: 96px;
            right: 28px;
            z-index: 9998;
            width: 370px;
            max-height: 520px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: chatSlideUp 0.3s ease;
            font-family: 'Inter', system-ui, sans-serif;
        }
        @keyframes chatSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .chat-panel-header {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chat-panel-header-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 15px;
        }
        .chat-panel-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .chat-panel-close:hover {
            background: rgba(255,255,255,0.3);
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-height: 280px;
            max-height: 340px;
            background: #f8fafc;
        }
        .chat-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #94a3b8;
            text-align: center;
        }
        .chat-empty-state p {
            font-weight: 600;
            margin: 0;
            font-size: 15px;
        }
        .chat-empty-state small {
            font-size: 13px;
        }
        .chat-bubble {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 16px;
            word-wrap: break-word;
            position: relative;
        }
        .chat-bubble-sent {
            align-self: flex-end;
            background: #3b82f6;
            color: white;
            border-bottom-right-radius: 4px;
        }
        .chat-bubble-received {
            align-self: flex-start;
            background: white;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
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
            max-width: 100%;
            border-radius: 8px;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .chat-attachment-img:hover { opacity: 0.9; }
        .chat-attachment-file {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
            font-size: 13px;
            transition: background 0.2s;
        }
        .chat-attachment-file:hover { background: rgba(255,255,255,0.3); }
        .chat-attachment-preview {
            padding: 8px 16px;
            background: #e0f2fe;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: #1e40af;
        }
        .chat-attachment-remove {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #ef4444;
            padding: 0 4px;
        }
        .chat-input-area {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            background: white;
            border-top: 1px solid #e2e8f0;
        }
        .chat-attach-btn {
            cursor: pointer;
            color: #64748b;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .chat-attach-btn:hover { color: #3b82f6; }
        .chat-file-hidden { display: none; }
        .chat-input {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 10px 16px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            background: #f8fafc;
        }
        .chat-input:focus { border-color: #3b82f6; background: white; }
        .chat-send-btn {
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
        .chat-send-btn:hover { background: #2563eb; transform: scale(1.05); }

        @media (max-width: 480px) {
            .chat-panel {
                width: calc(100vw - 24px);
                right: 12px;
                bottom: 88px;
                max-height: 70vh;
            }
            .chat-float-btn {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
            }
        }
    </style>

    @script
    <script>
        $wire.on('message-sent', () => {
            setTimeout(() => {
                const container = document.getElementById('chat-messages-container');
                if (container) container.scrollTop = container.scrollHeight;
            }, 100);
        });
        // Auto scroll on load
        document.addEventListener('livewire:navigated', () => {
            const container = document.getElementById('chat-messages-container');
            if (container) container.scrollTop = container.scrollHeight;
        });
    </script>
    @endscript
</div>
