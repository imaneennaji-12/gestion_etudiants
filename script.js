// Confirmation avant suppression
document.querySelectorAll('a.supprimer').forEach(function (lien) {
    lien.addEventListener('click', function (e) {
        if (!confirm('Voulez-vous vraiment supprimer cet étudiant ?')) {
            e.preventDefault();
        }
    });
});

// Recherche instantanée (sans rechargement)
var champ = document.getElementById('q');
if (champ) {
    champ.addEventListener('input', function () {
        var terme = champ.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(function (ligne) {
            var cellules = ligne.querySelectorAll('td');
            if (cellules.length < 3) return; // ligne "Aucun étudiant"
            var texte = (cellules[1].textContent + ' ' + cellules[2].textContent).toLowerCase();
            ligne.style.display = texte.includes(terme) ? '' : 'none';
        });
    });
}
