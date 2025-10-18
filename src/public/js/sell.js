// 商品出品画面用JavaScript

document.addEventListener('DOMContentLoaded', function () {
    // 画像アップロード処理
    const uploadButton = document.getElementById('uploadButton');
    const imageInput = document.getElementById('imageInput');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const imageRemove = document.getElementById('imageRemove');

    if (uploadButton) {
        uploadButton.addEventListener('click', function () {
            imageInput.click();
        });
    }

    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file) {
                // ファイルタイプのチェック
                if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
                    alert('JPEG または PNG 形式の画像を選択してください。');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    uploadPlaceholder.style.display = 'none';
                    imagePreview.style.display = 'flex';
                };

                reader.readAsDataURL(file);
            }
        });
    }

    if (imageRemove) {
        imageRemove.addEventListener('click', function () {
            imageInput.value = '';
            previewImg.src = '';
            uploadPlaceholder.style.display = 'flex';
            imagePreview.style.display = 'none';
        });
    }

    // カスタムドロップダウン処理
    const dropdown = document.getElementById('conditionDropdown');
    const dropdownSelected = document.getElementById('dropdownSelected');
    const selectedText = document.getElementById('selectedText');
    const dropdownOptions = document.getElementById('dropdownOptions');
    const conditionIdInput = document.getElementById('conditionId');

    if (dropdownSelected) {
        dropdownSelected.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdownOptions.classList.toggle('show');
            dropdownSelected.classList.toggle('active');
        });
    }

    if (dropdownOptions) {
        const options = dropdownOptions.querySelectorAll('.dropdown-option');

        options.forEach(function (option) {
            option.addEventListener('click', function () {
                const value = this.getAttribute('data-value');
                const text = this.textContent.trim();

                selectedText.textContent = text;
                conditionIdInput.value = value;
                dropdownOptions.classList.remove('show');
                dropdownSelected.classList.remove('active');
            });
        });
    }

    // ドロップダウン外クリック時に閉じる
    document.addEventListener('click', function (e) {
        if (dropdown && !dropdown.contains(e.target)) {
            dropdownOptions.classList.remove('show');
            dropdownSelected.classList.remove('active');
        }
    });

    // バリデーションエラー時の初期値設定
    const initialConditionId = conditionIdInput ? conditionIdInput.value : '';
    if (initialConditionId && dropdownOptions) {
        const selectedOption = dropdownOptions.querySelector('.dropdown-option[data-value="' + initialConditionId + '"]');
        if (selectedOption) {
            selectedText.textContent = selectedOption.textContent.trim();
        }
    }
});