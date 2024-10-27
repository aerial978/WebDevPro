document.addEventListener('DOMContentLoaded', function () {
    let inputFile = document.getElementById('image-post');
    let imagePreview = document.getElementById('imagePreview');
    let previewImage = document.createElement('img');

    if (inputFile) {
        inputFile.addEventListener('change', function () {
            if (inputFile.files && inputFile.files[0]) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.width = '150px';
                    previewImage.style.height = '100px';

                    while (imagePreview.firstChild) {
                        imagePreview.removeChild(previewImage.firstChild);
                    }

                    imagePreview.appendChild(previewImage);
                };

                reader.readAsDataURL(inputFile.files[0]);
            }
        });
    }
});




