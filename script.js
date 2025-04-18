
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector ("form[action='add_comment.php']") ;
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const data = new FormData(e.currentTarget) ;
        fetch ("add_comment.php", {
            method : "POST",
            body : data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            } // pour indiquer que la requête est envoyée par AJAX
        }) .then(r => {
            if (!r.ok)
                alert ("Erreur") ;
            else
                return r.json() ;
        }) .then(content => {
            const newcomment= document.createElement("div") ;
            newcomment.className = "card mt-3" ;
            newcomment.innerHTML = `
                <div class="card-body">
                    <p><strong>${content.pseudo}</strong> a écrit le ${content.date_creation} :</p>
                    <p>${content.comment.replace(/\n/g, '<br>')}</p>
                </div>
            ` ;
            document.getElementById("commentsection").appendChild(newcomment);
        }) 
    }) ;
});