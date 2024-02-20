const imageBtn = document.querySelector('.image-btn');
    const imageInput = document.querySelector('.image-input');
    const chooseImageLabel = document.querySelector('.choose-image-label');
    const fileNameDisplay = document.getElementById('fileName');
    
    imageBtn.addEventListener('click', function () {
        imageInput.click();
    });

    imageInput.addEventListener('change', function () {
        const file = imageInput.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imageBtn.style.backgroundImage = `url(${e.target.result})`;
                imageBtn.style.height = '100px';
                imageBtn.style.width = '175px';
                imageBtn.style.border = 'none';
                chooseImageLabel.style.display = 'none';
            };
            reader.readAsDataURL(file);

            fileNameDisplay.textContent = file.name;
        } else {
            fileNameDisplay.textContent = '';
        }
    });