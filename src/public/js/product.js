// 商品詳細ページ用JavaScript

document.addEventListener('DOMContentLoaded', function () {
    const likeButton = document.querySelector('.like-button');

    if (likeButton) {
        likeButton.addEventListener('click', function () {
            const itemId = this.getAttribute('data-item-id');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/item/${itemId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.liked) {
                        this.classList.add('liked');
                    } else {
                        this.classList.remove('liked');
                    }

                    this.querySelector('.likes-count').textContent = data.likes_count;
                })
                .catch(error => console.error('Error:', error));
        });
    }
});