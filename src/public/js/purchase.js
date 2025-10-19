// 商品購入画面用JavaScript

document.addEventListener('DOMContentLoaded', function () {
    const dropdown = document.getElementById('paymentDropdown');
    const dropdownSelected = document.getElementById('paymentSelected');
    const dropdownOptions = document.getElementById('paymentOptions');
    const paymentDisplay = document.getElementById('paymentDisplay');
    const paymentMethodInput = document.getElementById('paymentMethodInput');

    if (!dropdown || !dropdownSelected || !dropdownOptions) {
        return;
    }

    // ドロップダウンの開閉
    dropdownSelected.addEventListener('click', function () {
        dropdownOptions.classList.toggle('show');
    });

    // オプション選択時の処理
    document.querySelectorAll('.dropdown-option').forEach(option => {
        option.addEventListener('click', function () {
            const value = this.getAttribute('data-value');
            const text = this.textContent;

            dropdownSelected.textContent = text;

            if (paymentDisplay) {
                paymentDisplay.textContent = text;
            }

            if (paymentMethodInput) {
                paymentMethodInput.value = value;
            }

            dropdownOptions.classList.remove('show');

            // 選択状態のスタイル更新
            document.querySelectorAll('.dropdown-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            this.classList.add('selected');
        });
    });

    // ドロップダウン外クリック時に閉じる
    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target)) {
            dropdownOptions.classList.remove('show');
        }
    });
});