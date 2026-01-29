// 編集ボタンのイベントリスナー
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-message-btn');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const messageId = this.dataset.messageId;
            const messageText = this.dataset.messageText;
            const updateUrl = this.dataset.updateUrl;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            editMessage(messageId, messageText, updateUrl, csrfToken);
        });
    });

    // 評価フォームの送信前チェック
    const ratingForm = document.getElementById('ratingForm');
    if (ratingForm) {
        ratingForm.addEventListener('submit', function (e) {
            const ratingValue = document.getElementById('ratingValue').value;
            if (ratingValue === '0' || ratingValue === '') {
                e.preventDefault();
                alert('評価を選択してください。');
                return false;
            }
        });
    }
});

// 入力情報保持機能
const messageInput = document.getElementById('messageInput');
const messageForm = document.getElementById('messageForm');

if (messageInput && messageForm) {
    const transactionId = window.location.pathname.split('/').pop();
    const storageKey = `transaction_message_${transactionId}`;

    const savedMessage = sessionStorage.getItem(storageKey);
    if (savedMessage && messageInput.value === '') {
        messageInput.value = savedMessage;
    }

    messageInput.addEventListener('input', function () {
        if (this.value.trim() !== '') {
            sessionStorage.setItem(storageKey, this.value);
        } else {
            sessionStorage.removeItem(storageKey);
        }
    });

    messageForm.addEventListener('submit', function () {
        sessionStorage.removeItem(storageKey);
    });
}

function editMessage(messageId, currentText, updateUrl, csrfToken) {
    const messageTextElement = document.getElementById('message-text-' + messageId);
    const messageContent = messageTextElement.parentElement;

    // 編集フォームを作成
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = updateUrl;
    form.style.width = '100%';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);

    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'PUT';
    form.appendChild(methodField);

    const textarea = document.createElement('textarea');
    textarea.name = 'message';
    textarea.value = currentText;
    textarea.style.width = '100%';
    textarea.style.minHeight = '60px';
    textarea.style.padding = '8px';
    textarea.style.borderRadius = '4px';
    textarea.style.border = '1px solid #5F5F5F';
    textarea.style.fontFamily = 'Inter, sans-serif';
    textarea.style.fontSize = '16px';
    form.appendChild(textarea);

    const buttonContainer = document.createElement('div');
    buttonContainer.style.marginTop = '8px';
    buttonContainer.style.display = 'flex';
    buttonContainer.style.gap = '8px';

    const saveButton = document.createElement('button');
    saveButton.type = 'submit';
    saveButton.textContent = '保存';
    saveButton.style.padding = '6px 12px';
    saveButton.style.background = '#FF5555';
    saveButton.style.color = 'white';
    saveButton.style.border = 'none';
    saveButton.style.borderRadius = '4px';
    saveButton.style.cursor = 'pointer';
    buttonContainer.appendChild(saveButton);

    const cancelButton = document.createElement('button');
    cancelButton.type = 'button';
    cancelButton.textContent = 'キャンセル';
    cancelButton.style.padding = '6px 12px';
    cancelButton.style.background = '#999';
    cancelButton.style.color = 'white';
    cancelButton.style.border = 'none';
    cancelButton.style.borderRadius = '4px';
    cancelButton.style.cursor = 'pointer';
    cancelButton.onclick = function () {
        messageContent.innerHTML = originalContent;
    };
    buttonContainer.appendChild(cancelButton);

    form.appendChild(buttonContainer);

    // 元のコンテンツを保存
    const originalContent = messageContent.innerHTML;

    // フォームに置き換え
    messageContent.innerHTML = '';
    messageContent.appendChild(form);

    textarea.focus();
}

function setRating(value) {
    document.getElementById('ratingValue').value = value;

    const stars = document.querySelectorAll('.star-rate');
    const filledStarUrl = document.querySelector('.star-rate').dataset.filledUrl;
    const emptyStarUrl = document.querySelector('.star-rate').dataset.emptyUrl;

    stars.forEach((star, index) => {
        if (index < value) {
            star.src = filledStarUrl;
        } else {
            star.src = emptyStarUrl;
        }
    });
}