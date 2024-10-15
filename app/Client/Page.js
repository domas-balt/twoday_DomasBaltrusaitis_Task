const form = document.querySelector('#postWordForm');

async function sendData() {
    const formData = new FormData(form);

    var object = {};
    formData.forEach(function(value, key){
        object[key] = value;
    });
    var json = JSON.stringify(object);

    try {
        const response = await fetch("http://127.0.0.1:8080/words", {
            method: "POST",
            body: json
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
