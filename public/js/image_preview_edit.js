document.addEventListener('DOMContentLoaded', function () {
    let inputFiles = document.getElementsByName('image-post');
    let existingImagePreview = document.getElementById('existingImagePreview');
    let existingFileInfo = document.getElementById('existingFileInfo');
    
    if (inputFiles.length > 0) {
        let inputFile = inputFiles[0];

        if (inputFile) {
            inputFile.addEventListener('change', function () {
                if (inputFile.files && inputFile.files[0]) {
                    let reader = new FileReader();

                    reader.onload = function (e) {
                        existingImagePreview.src = e.target.result;
                        existingImagePreview.style.width = '150px';
                        existingImagePreview.style.height = '100px';

                        existingFileInfo.querySelector('p').textContent = '';
                    };

                    reader.readAsDataURL(inputFile.files[0]);
                }
            });
        }
    }
});
