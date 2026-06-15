/**
 * Lightweight AI assistant demo.
 *
 * Keeps the interface interactive until the recommendation API is connected.
 */
export function initAiAssistant() {
    const root = document.querySelector('[data-ai-assistant]');
    if (!root) return;

    const form = root.querySelector('[data-ai-form]');
    const input = root.querySelector('[data-ai-input]');
    const messages = root.querySelector('[data-ai-messages]');
    const sendButton = form?.querySelector('button[type="submit"]');
    const response = root.dataset.demoResponse || '';

    if (!form || !input || !messages || !sendButton) return;

    const addMessage = (text, type) => {
        const message = document.createElement('div');
        const bubble = document.createElement('p');

        message.className = `ai-message ai-message--${type}`;
        bubble.textContent = text;
        message.appendChild(bubble);
        messages.appendChild(message);
        messages.scrollTop = messages.scrollHeight;

        return message;
    };

    const submitMessage = () => {
        const value = input.value.trim();
        if (!value || sendButton.disabled) return;

        addMessage(value, 'user');
        input.value = '';
        input.focus();
        sendButton.disabled = true;

        const typing = addMessage('...', 'assistant ai-message--typing');

        window.setTimeout(() => {
            typing.remove();
            addMessage(response, 'assistant');
            sendButton.disabled = false;
        }, 650);
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        submitMessage();
    });

    root.querySelectorAll('[data-ai-example]').forEach((button) => {
        button.addEventListener('click', () => {
            input.value = button.dataset.aiExample || button.textContent.trim();
            submitMessage();
        });
    });
}
