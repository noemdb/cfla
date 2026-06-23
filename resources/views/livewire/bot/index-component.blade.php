<div
    x-data="{
        messages: @entangle('messages'),
        isTyping: @entangle('isTyping'),
        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.scrollTarget;
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'end' });
            });
        }
    }"
    x-init="$nextTick(() => scrollToBottom())"
    x-effect="messages.length; isTyping; $nextTick(() => $nextTick(() => scrollToBottom()))"
    class="flex-1 flex flex-col min-h-0"
>
    <!-- Chat Messages Area -->
    <div
        id="chat-messages"
        x-ref="chatContainer"
        class="flex-1 overflow-y-auto px-4 py-4 space-y-2"
        style="background: #0b141a; scroll-behavior: smooth;"
    >
        <template x-for="(msg, index) in messages" :key="index">
            <div
                class="flex message-enter"
                :class="msg.type === 'user' ? 'justify-end' : 'justify-start'"
            >
                <!-- Bot message with avatar -->
                <template x-if="msg.type === 'bot'">
                    <div class="flex items-end gap-2 max-w-[85%] sm:max-w-[75%]">
                        <div class="w-7 h-7 rounded-full bg-emerald-600/20 flex items-center justify-center text-sm flex-shrink-0 mb-0.5">
                            🤖
                        </div>
                        <div
                            class="px-3.5 py-2.5 text-sm leading-relaxed shadow-sm"
                            style="background: #1f2c33; color: #e9edef; border-radius: 12px 12px 12px 4px;"
                        >
                            <div x-html="formatBotText(msg.text)" class="break-words whitespace-pre-wrap"></div>
                            <div class="flex items-center gap-1 mt-1.5">
                                <span class="text-[10px] opacity-50" x-text="msg.time"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- User message with double check -->
                <template x-if="msg.type === 'user'">
                    <div class="flex items-end gap-2 max-w-[85%] sm:max-w-[75%]">
                        <div
                            class="px-3.5 py-2.5 text-sm leading-relaxed shadow-sm"
                            style="background: #005c4b; color: #e9edef; border-radius: 12px 12px 4px 12px;"
                        >
                            <div x-html="escapeHtml(msg.text)" class="break-words"></div>
                            <div class="flex items-center justify-end gap-1 mt-1.5">
                                <span class="text-[10px] opacity-50" x-text="msg.time"></span>
                                <svg class="w-3.5 h-3.5 text-emerald-400/70" viewBox="0 0 16 11" fill="currentColor">
                                    <path d="M11.071.653a.457.457 0 0 0-.304-.102.493.493 0 0 0-.381.178l-6.19 7.636-2.011-2.095a.463.463 0 0 0-.336-.153.457.457 0 0 0-.334.145.552.552 0 0 0-.139.37c0 .14.046.27.139.385l2.368 2.466a.463.463 0 0 0 .335.153.458.458 0 0 0 .306-.116l6.718-8.273a.532.532 0 0 0 .118-.337.505.505 0 0 0-.14-.352z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Typing Indicator -->
        <div x-show="isTyping" class="flex justify-start">
            <div class="flex items-end gap-2 max-w-[85%] sm:max-w-[75%]">
                <div class="w-7 h-7 rounded-full bg-emerald-600/20 flex items-center justify-center text-sm flex-shrink-0 mb-0.5">🤖</div>
                <div style="background: #1f2c33; border-radius: 12px 12px 12px 4px;" class="px-4 py-3">
                    <div class="flex gap-1.5">
                        <span class="typing-dot w-2 h-2 bg-gray-400 rounded-full inline-block"></span>
                        <span class="typing-dot w-2 h-2 bg-gray-400 rounded-full inline-block"></span>
                        <span class="typing-dot w-2 h-2 bg-gray-400 rounded-full inline-block"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll anchor -->
        <div x-ref="scrollTarget"></div>
    </div>

    <!-- Input Area -->
    <div class="bg-gray-800 border-t border-gray-700 px-3 py-2.5 flex-shrink-0">
        <form
            wire:submit.prevent="sendMessage"
            class="flex items-center gap-2"
        >
            <div class="flex-1 flex items-center bg-gray-900 rounded-full border border-gray-700 focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500 transition-all duration-200">
                <input
                    type="text"
                    wire:model="newMessage"
                    placeholder="Escribe un mensaje..."
                    class="flex-1 bg-transparent text-gray-100 px-4 py-2.5 text-sm outline-none placeholder-gray-500 min-h-[44px]"
                    x-on:keydown.enter.prevent="$wire.sendMessage()"
                    autofocus
                    autocomplete="off"
                    inputmode="text"
                    enterkeyhint="send"
                >
            </div>
            <button
                type="submit"
                class="touch-btn bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-400 text-white rounded-full min-w-[44px] h-[44px] flex items-center justify-center transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-emerald-900/30"
                wire:loading.attr="disabled"
                aria-label="Enviar mensaje"
            >
                <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>
        <p class="text-[10px] text-gray-500 mt-1.5 text-center">
            Este asistente proporciona información académica y administrativa.
        </p>
    </div>

    <script>
        function formatBotText(text) {
            if (!text) return '';
            // Escape HTML
            let html = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            // Bold: *text*
            html = html.replace(/\*(.+?)\*/g, '<strong>$1</strong>');
            // Code: ```text```  or `text`
            html = html.replace(/```(.+?)```/gs, '<code class="bg-gray-800/80 px-1.5 py-0.5 rounded text-emerald-300 text-xs font-mono">$1</code>');
            html = html.replace(/`([^`]+)`/g, '<code class="bg-gray-800/80 px-1 rounded text-emerald-300 text-xs font-mono">$1</code>');
            // Newlines to <br>
            html = html.replace(/\n/g, '<br>');
            return html;
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
        }
    </script>
</div>
