const form = document.querySelector('#postWordForm');

async function sendData() {
    const formData = new FormData(form);
    console.log(formData.get('word'));

    try {
        const response = await fetch("http://127.0.0.1:8000/words", {
            method: "POST",
            body: JSON.stringify({text: formData.get('word')})
        });
        console.log(await response.json());
    } catch (e) {
        console.error(e);
    }
}

form.addEventListener("submit", (event) => {
    event.preventDefault();
    sendData();
});
