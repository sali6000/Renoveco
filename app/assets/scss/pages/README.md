En cas d'ajout d'une nouvelle page exemple: contact.scss :
    - Rajouter une nouvelle entrée spécifique scss dans le webpack.config.js :
        Exemple: .addStyleEntry('contact', './app/assets/scss/pages/contact.scss')
    - Préciser la "current_page" dans le controller ContactController.php :
        Exemple: $this->set('current_page', 'contact');
        
    (Afin d'établir une liaison entre la vue (via Views/layout.twig) et le scss appellé depuis le controller (via current_page))