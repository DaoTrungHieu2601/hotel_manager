import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('customerChat', (cfg) => ({
        open: false,
        messages: [],
        body: '',
        sending: false,
        error: '',
        pollInterval: null,
        fetchUrl: cfg.fetchUrl,
        sendUrl: cfg.sendUrl,
        errLoad: cfg.errLoad ?? 'Không tải được tin nhắn.',
        errSend: cfg.errSend ?? 'Không gửi được. Thử lại sau.',
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.error = '';
                this.load();
                this.startPoll();
            } else {
                this.stopPoll();
            }
        },
        startPoll() {
            this.stopPoll();
            this.pollInterval = window.setInterval(() => this.load(), 5000);
        },
        stopPoll() {
            if (this.pollInterval !== null) {
                window.clearInterval(this.pollInterval);
                this.pollInterval = null;
            }
        },
        async load() {
            try {
                const { data } = await window.axios.get(this.fetchUrl);
                this.messages = data.messages ?? [];
            } catch {
                this.error = this.errLoad;
            }
        },
        async send() {
            const text = this.body.trim();
            if (!text || this.sending) {
                return;
            }
            this.sending = true;
            this.error = '';
            try {
                await window.axios.post(this.sendUrl, { body: text });
                this.body = '';
                await this.load();
            } catch {
                this.error = this.errSend;
            } finally {
                this.sending = false;
            }
        },
    }));
});

Alpine.start();
