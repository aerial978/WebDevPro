document.addEventListener('DOMContentLoaded', function () {
    const moreButton = document.getElementById('show-more-tags');
    const hiddenTags = document.querySelectorAll('.tag-cloud .d-none');

    moreButton.addEventListener('click', function () {
        hiddenTags.forEach(tag => {
            tag.classList.remove('d-none');
        });
        moreButton.style.display = 'none';
    });
});