document.addEventListener("DOMContentLoaded", function(event) {
    let statusesSelect = document.getElementById('status-filter');
    statusesSelect.onchange = function(e){
        if (!e) {
            e = window.event;
        }
        let svalue = this.options[this.selectedIndex].value;
        if (svalue == 0) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete('status');
            window.location.href = (`${window.location.origin}${window.location.pathname}?${urlParams.toString()}`).replace('?','');
        } else {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('status', svalue);
            window.location.href = `${window.location.origin}${window.location.pathname}?${urlParams.toString()}`;
        }
    }
});