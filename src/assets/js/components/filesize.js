Filevalidation = () => {
    const fi = document.getElementById('fichier');
    var inputElement = document.getElementById('fichier').files[0].type;
    // Check if any file is selected.
    if (fi.files.length > 0) {
        for (var i = 0; i <= fi.files.length - 1; i++) {

            var fsize = fi.files.item(i).size;
            var file = Math.round((fsize / 1024));
            // The size of the file.
            if (file >= 10240) {
                fi.value = '';
                document.getElementById('fileSizeMax').innerHTML = "Veuillez sélectionner un fichier PDF d'une taille de maximum de 10Mo. Votre fichier dépasse la taille maximale.";
                document.getElementById('fileSizeMax').style.display = 'block';
            } else if (fi.files[0].type != "application/pdf") {
                fi.value = '';
                document.getElementById('fileSizeMax').innerHTML = "Le fichier n'est pas au format PDF, veuillez sélectionner un fichier au format PDF.";
                document.getElementById('fileSizeMax').style.display = 'block';
            } else {
                document.getElementById('fileSizeMax').style.display = 'none';
            }
        }
    }
}