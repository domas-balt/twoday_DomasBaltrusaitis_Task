async function toggleDelete(o, id){
    let p = o.parentNode.parentNode;
    p.parentNode.removeChild(p);

    let url = uri + `/words/${id}`

    try {
        await fetch(url, {
            method: 'DELETE'
        });
    } catch (e) {
        console.error(e);
    }
}
