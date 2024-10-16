
async function toggleDelete(o, id){
    let p = o.parentNode.parentNode;
    p.parentNode.removeChild(p);

    let url = `http://127.0.0.1:8000/words/${id}`

    try {
        await fetch(url, {
            method: 'DELETE'
        });
    } catch (e) {
        console.error(e);
    }
}
