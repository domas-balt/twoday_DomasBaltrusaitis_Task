function toggleUpdate(id, word) {
    let listDiv = document.getElementById('contentDiv')
    let div = document.createElement('div')
    div.innerHTML = `<label htmlFor="wordUpdate">Your word:</label>` + "<br>" +
        `<input type="text" placeholder="${word}" id="wordUpdate" name="wordUpdate" required>` +
        `<button class="btn updtbtn" onclick="updateData('${id}')">Update</button>`
    listDiv.appendChild(div);
}

function updateData(id, word){
    let route = uri + `/words/${id}`
    let wordNew = document.getElementById('wordUpdate').value;
    fetch(route, {
        method: 'PUT',
        body: JSON.stringify({text: wordNew})
    });
    location.reload()
}
