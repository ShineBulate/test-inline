document.getElementById('search_form').addEventListener('submit', function(event) {
    event.preventDefault();

    const searchText = document.getElementById('search_text').value;

    if (searchText.length < 3) {
        alert('Пожалуйста, введите минимум 3 символа для поиска.');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `http/Controller/SearchController.php?query=${encodeURIComponent(searchText)}`, true);

    xhr.onload = function() {
        const resultsContainer = document.getElementById('results');
        resultsContainer.innerHTML = '';
    
        if (xhr.status === 200) {
            console.log('Ответ от сервера:', xhr.responseText); // Выводим ответ в консоль
            const results = JSON.parse(xhr.responseText);
            
            if (results.error) {
                resultsContainer.innerHTML = `<p>${results.error}</p>`;
            } else {
                results.forEach(comment => {
                    const resultItem = document.createElement('div');
                    resultItem.innerHTML = `<h3>${`Пост: `+comment.post_title}:</h3><p>${`Комментарий: `+comment.comment_body}</p>`;
                    resultsContainer.appendChild(resultItem);
                });
            }
        } else {
            console.error('Ошибка при выполнении запроса:', xhr.statusText);
        }
    };

    xhr.onerror = function() {
        console.error('Ошибка сети');
    };

    xhr.send(); 
});