const form = document.querySelector('#postWordForm');
const statUpdate = document.querySelector('#statUpdate')
async function sendData() {
    const formData = new FormData(form);

    try {
        await fetch(uri + '/words', {
            method: 'POST',
            body: JSON.stringify({text: formData.get('word')})
        });

        statUpdate.innerHTML = "Word created!";

        setTimeout(function() {
            statUpdate.innerHTML = ""
        }, 2000);
    } catch (e) {
        console.error(e);
    }
}

form.addEventListener('submit', (event) => {
    event.preventDefault();
    sendData();
});
