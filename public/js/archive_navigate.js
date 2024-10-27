document.addEventListener('DOMContentLoaded', function () {
    window.navigateToArchive = function (selectElement) {
        var url = selectElement.value;
        if (url) {
            window.location.href = url;
        }
    };
});
