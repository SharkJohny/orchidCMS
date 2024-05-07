import Sortable from 'sortablejs';


document.addEventListener('DOMContentLoaded', (event) => {
    addSortable();


});

document.addEventListener("turbo:load", () => {
    addSortable();
})

function addSortable() {
    console.log('Platform JS loadedss');
    if (document.querySelector('.page-move') == null) {
        console.log()
        return
    }

    new Sortable(document.querySelector('.table tbody'), {
        handle: 'tr', // handle's class
        animation: 150
    });
}