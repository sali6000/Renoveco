    document.querySelectorAll('.year-title').forEach(title => {
        console.log('hello');

        title.addEventListener('click', () => {
            title.nextElementSibling.classList.toggle('hidden');
        });
    });