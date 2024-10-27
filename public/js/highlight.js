document.addEventListener('DOMContentLoaded', function () {
    // Récupère les termes de recherche depuis la variable Twig et les convertit en format JSON
    let searchTerms = window.searchTerms;
    // Sélectionne l'élément HTML avec l'ID 'post-list' qui contient les posts
    let postsContainer = document.getElementById('post-list');

    // Fonction pour échapper les caractères spéciaux d'une chaîne
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Fonction pour mettre en évidence les termes recherchés dans les nœuds de texte
    function highlightText(element, searchTerms) {
        // Parcourt chaque terme de recherche
        searchTerms.forEach(str => {
            // gi : combinaison d'indicateurs
            // g (global) : permet à l'expression régulière de trouver toutes les correspondances dans le texte, pas seulement la première
            // i (ignore case) :  permet à l'expression régulière de faire des correspondances insensibles à la casse
            let regex = new RegExp(`(${escapeRegExp(str)})`, 'gi');
            highlight(element, regex);
        });
    }

    // Fonction récursive pour parcourir les nœuds et mettre en évidence le texte
    function highlight(node, regex) {
        if (node.nodeType === 3) { // Noeud de texte
            let match = node.nodeValue.match(regex);
            if (match) {
                let span = document.createElement('span');
                span.innerHTML = node.nodeValue.replace(regex, '<span class="highlight">$1</span>');
                node.replaceWith(span);
            }
        } else if (node.nodeType === 1 && node.childNodes && !/(script|style|a)/i.test(node.tagName)) {
            node.childNodes.forEach(child => highlight(child, regex));
        }
    }

    // Sélectionner les éléments de titre et de contenu pour chaque post
    let postTitles = postsContainer.querySelectorAll('.blog-title');
    let postContents = postsContainer.querySelectorAll('.blog-content');
    
    // Mettre en évidence les termes de recherche dans les titres
    postTitles.forEach(title => highlightText(title, searchTerms));

    // Mettre en évidence les termes de recherche dans les contenus
    postContents.forEach(content => highlightText(content, searchTerms));
});