document.addEventListener('DOMContentLoaded', function () {
    let pageLink = document.querySelectorAll('.page-link');
    let numberOfPages = pageLink.length -2;

    pageLink.forEach(link => {
        link.addEventListener('click', function (event) {
            console.log('test');
            let dataPage = link.getAttribute('data-page');

            if(parseInt(dataPage)-1 <= 0) {
                let previousButton = document.getElementById('previous-button');
                previousButton.removeAttribute('data-page');
                previousButton.setAttribute('disabled',true);
            }else{
                let previousButton = document.getElementById('previous-button');
                previousButton.setAttribute('data-page', parseInt(dataPage)-1);
                previousButton.setAttribute('disabled',false);
            }
            
            let nextButton = document.getElementById('next-button');
            nextButton.setAttribute('data-page', parseInt(dataPage)+1);
            
        });

    });

}

);