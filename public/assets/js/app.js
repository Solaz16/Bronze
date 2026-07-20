document.addEventListener('DOMContentLoaded', function () {
    var pageEntry = document.body.classList.contains('page-entry');
    var barre = document.createElement('div');
    barre.className = 'barre-scroll';
    document.body.appendChild(barre);

    var toast = document.createElement('div');
    toast.className = 'toast';
    document.body.appendChild(toast);

    function afficherToast(texte) {
        toast.textContent = texte;
        toast.classList.add('visible');
        setTimeout(function () {
            toast.classList.remove('visible');
        }, 2200);
    }

    if (pageEntry) {
        var entryAudio = document.querySelector('#entryAudio');
        var entryStart = document.querySelector('#entryStart');

        function jouerEntry() {
            if (!entryAudio) {
                return;
            }

            entryAudio.loop = true;
            entryAudio.volume = 0.9;
            entryAudio.muted = false;
            entryAudio.play().catch(function () {
                if (entryStart) {
                    entryStart.classList.add('visible');
                }
            });
        }

        if (entryStart) {
            entryStart.addEventListener('click', jouerEntry);
        }

        setTimeout(jouerEntry, 450);
    }

    for (var i = 0; i < 18; i++) {
        var particule = document.createElement('span');
        particule.className = 'particule';
        particule.style.left = Math.floor(Math.random() * 100) + '%';
        particule.style.animationDelay = (Math.random() * 9) + 's';
        particule.style.animationDuration = (7 + Math.random() * 8) + 's';
        document.body.appendChild(particule);
    }

    document.addEventListener('mousemove', function (event) {
        document.body.style.setProperty('--souris-x', event.clientX + 'px');
        document.body.style.setProperty('--souris-y', event.clientY + 'px');
    });

    var chemin = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('nav a').forEach(function (lien) {
        if (lien.getAttribute('href') === chemin) {
            lien.classList.add('actif');
        }
    });

    var blocs = document.querySelectorAll('.bloc, .recommandation-blame, tbody tr, .carte-accueil, .carte-livre');

    blocs.forEach(function (bloc, index) {
        bloc.classList.add('revele');
        setTimeout(function () {
            bloc.classList.add('visible');
        }, 60 + index * 35);
    });

    var ligneBlame = document.querySelector('.ligne-blame');

    if (ligneBlame) {
        ligneBlame.style.boxShadow = 'inset 5px 0 0 #74c0fc';
    }

    document.querySelectorAll('.jaquette').forEach(function (jaquette) {
        jaquette.addEventListener('mouseenter', function () {
            jaquette.classList.add('survol');
        });

        jaquette.addEventListener('mouseleave', function () {
            jaquette.classList.remove('survol');
        });
    });

    function initialiserCarte(carte) {
        if (carte.dataset.initialisee === '1') {
            return;
        }

        carte.dataset.initialisee = '1';
        var titre = carte.querySelector('h3') ? carte.querySelector('h3').textContent.trim() : '';
        var favori = document.createElement('button');
        favori.type = 'button';
        favori.className = 'mini-favori';
        favori.textContent = '\u2605';
        favori.setAttribute('aria-label', 'Favori');
        carte.appendChild(favori);

        if (localStorage.getItem('favori_' + titre) === '1') {
            carte.classList.add('favori');
            favori.classList.add('actif');
        }

        favori.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var actif = !carte.classList.contains('favori');
            carte.classList.toggle('favori', actif);
            favori.classList.toggle('actif', actif);
            localStorage.setItem('favori_' + titre, actif ? '1' : '0');
            document.dispatchEvent(new CustomEvent('favorisChange'));
            afficherToast(actif ? titre + ' ajoute aux favoris' : titre + ' retire des favoris');
        });

        var miniProgression = carte.querySelector('[data-progression-mini] span');
        var progression = localStorage.getItem('progression_' + titre) || '0';
        if (miniProgression) {
            miniProgression.style.width = progression + '%';
        }

        carte.addEventListener('mousemove', function (event) {
            var rectangle = carte.getBoundingClientRect();
            var x = event.clientX - rectangle.left;
            var y = event.clientY - rectangle.top;
            var rotationX = ((y / rectangle.height) - 0.5) * -5;
            var rotationY = ((x / rectangle.width) - 0.5) * 5;
            carte.style.transform = 'translateY(-5px) rotateX(' + rotationX + 'deg) rotateY(' + rotationY + 'deg)';
        });

        carte.addEventListener('mouseleave', function () {
            carte.style.transform = '';
        });
    }

    document.querySelectorAll('.carte-livre').forEach(initialiserCarte);

    var recherche = document.querySelector('#recherche');
    var cartes = document.querySelectorAll('.carte-livre');
    var compteurPage = document.querySelector('[data-catalogue-page-count]');
    var formulaireCatalogue = document.querySelector('[data-catalogue-form]');
    var champRechercheCatalogue = document.querySelector('[data-catalogue-search]');
    var debounceCatalogue = null;

    function soumettreCatalogue(delai) {
        if (!formulaireCatalogue) {
            return;
        }

        window.clearTimeout(debounceCatalogue);
        debounceCatalogue = window.setTimeout(function () {
            if (typeof formulaireCatalogue.requestSubmit === 'function') {
                formulaireCatalogue.requestSubmit();
            } else {
                formulaireCatalogue.submit();
            }
        }, delai);
    }

    function filtrerCartes() {
        if (!recherche || cartes.length === 0) {
            return;
        }

        var texte = recherche.value.toLowerCase().trim();
        var visibles = 0;
        var seulementFavoris = document.body.classList.contains('filtre-favoris-actif');

        cartes.forEach(function (carte) {
            var contenu = carte.textContent.toLowerCase();
            var visible = contenu.indexOf(texte) !== -1 && (!seulementFavoris || carte.classList.contains('favori'));
            carte.classList.toggle('cachee', !visible);

            if (visible) {
                visibles++;
            }
        });

        if (compteurPage) {
            compteurPage.textContent = visibles + ' manga(s) visibles sur ' + cartes.length + ' sur cette page.';
        }

        var aucun = document.querySelector('.aucun-resultat');
        if (aucun) {
            aucun.hidden = visibles !== 0;
        }
    }

    if (recherche && cartes.length > 0) {
        recherche.addEventListener('input', filtrerCartes);
    }

    if (formulaireCatalogue) {
        formulaireCatalogue.querySelectorAll('select').forEach(function (champ) {
            champ.addEventListener('change', function () {
                soumettreCatalogue(100);
            });
        });
    }

    if (champRechercheCatalogue) {
        champRechercheCatalogue.addEventListener('input', function () {
            filtrerCartes();
            soumettreCatalogue(350);
        });
    }

    var filtreFavoris = document.querySelector('[data-filtre-favoris]');
    if (filtreFavoris) {
        filtreFavoris.addEventListener('click', function () {
            var actif = document.body.classList.toggle('filtre-favoris-actif');
            filtreFavoris.classList.toggle('actif', actif);
            filtreFavoris.textContent = actif ? 'Voir tous les mangas' : 'Mes favoris';
            filtrerCartes();
        });
    }

    var surprise = document.querySelector('[data-surprise]');
    if (surprise) {
        surprise.addEventListener('click', function () {
            var disponibles = Array.prototype.filter.call(cartes, function (carte) {
                return !carte.classList.contains('cachee');
            });
            if (disponibles.length === 0) {
                afficherToast('Aucun manga a choisir');
                return;
            }
            var choix = disponibles[Math.floor(Math.random() * disponibles.length)];
            choix.classList.add('choix-surprise');
            choix.scrollIntoView({ behavior: 'smooth', block: 'center' });
            afficherToast('Essaie ' + choix.dataset.titre);
            setTimeout(function () { choix.classList.remove('choix-surprise'); }, 1800);
        });
    }

    var detailLivre = document.querySelector('[data-detail-livre]');
    if (detailLivre) {
        var progressionInput = detailLivre.querySelector('[data-progression]');
        var progressionTexte = detailLivre.querySelector('[data-progression-texte]');
        var titreDetail = detailLivre.dataset.titre;
        var progressionSauvee = localStorage.getItem('progression_' + titreDetail) || '0';
        if (progressionInput) {
            progressionInput.value = progressionSauvee;
            progressionTexte.textContent = progressionSauvee + '%';
            progressionInput.addEventListener('input', function () {
                localStorage.setItem('progression_' + titreDetail, progressionInput.value);
                progressionTexte.textContent = progressionInput.value + '%';
                afficherToast('Progression enregistree');
            });
        }
    }

    function afficherFavoris() {
        var grilleFavoris = document.querySelector('[data-favoris-grille]');
        if (!grilleFavoris) {
            return;
        }
        fetch('livres.json.php').then(function (reponse) { return reponse.json(); }).then(function (livres) {
            grilleFavoris.innerHTML = '';
            var favoris = livres.filter(function (livre) { return localStorage.getItem('favori_' + livre.titre) === '1'; });
            document.querySelector('[data-favoris-vide]').hidden = favoris.length > 0;
            favoris.forEach(function (livre) {
                var carte = document.createElement('article');
                carte.className = 'carte-livre favori';
                carte.dataset.titre = livre.titre;
                carte.innerHTML = '<a class="carte-image" href="livre.php?id=' + livre.id + '"><div class="jaquette">' + (livre.couverture ? '<img src="' + livre.couverture + '" alt="Jaquette de ' + livre.titre + '">' : '') + '</div></a><div class="carte-contenu"><h3>' + livre.titre + '</h3><p>' + livre.auteur + '</p><p>' + livre.categorie + '</p><div class="progression-mini"><span></span></div></div><div class="carte-actions"><a class="bouton" href="livre.php?id=' + livre.id + '">Voir</a></div>';
                grilleFavoris.appendChild(carte);
                initialiserCarte(carte);
            });
        }).catch(function () { afficherToast('Impossible de charger les favoris'); });
    }

    afficherFavoris();
    document.addEventListener('favorisChange', afficherFavoris);

    var boutonSynopsis = document.querySelector('[data-synopsis]');
    var champTitre = document.querySelector('#titre');
    var champResume = document.querySelector('#resume');
    var jaquettePreview = document.querySelector('[data-jaquette-preview]');
    var jaquettePreviewImage = jaquettePreview ? jaquettePreview.querySelector('.jaquette-preview-image') : null;
    var jaquettePreviewTexte = document.querySelector('[data-jaquette-preview-texte]');
    var debounceJaquette = null;

    function afficherApercuJaquette(url, texte) {
        if (!jaquettePreview || !jaquettePreviewImage || !jaquettePreviewTexte) {
            return;
        }

        if (!url) {
            jaquettePreview.hidden = true;
            return;
        }

        jaquettePreview.hidden = false;
        jaquettePreviewImage.style.backgroundImage = 'url("' + url.replace(/"/g, '%22') + '")';
        jaquettePreviewTexte.textContent = texte;
    }

    function rechercherJaquetteAuto() {
        if (!jaquettePreview || !champTitre) {
            return;
        }

        var titre = champTitre.value.trim();
        var auteur = champAuteur ? champAuteur.value.trim() : '';

        if (titre === '') {
            afficherApercuJaquette('', '');
            return;
        }

        window.clearTimeout(debounceJaquette);
        debounceJaquette = window.setTimeout(function () {
            fetch('jaquette_auto.php?titre=' + encodeURIComponent(titre) + '&auteur=' + encodeURIComponent(auteur))
                .then(function (reponse) { return reponse.json(); })
                .then(function (data) {
                    if (!data.ok || !data.url) {
                        afficherApercuJaquette('', 'Aucune jaquette automatique trouvee.');
                        return;
                    }

                    afficherApercuJaquette(data.url, data.source === 'openlibrary' ? 'Jaquette trouvee automatiquement sur Open Library.' : 'Jaquette trouvee automatiquement.');
                })
                .catch(function () {
                    afficherApercuJaquette('', 'Recherche de jaquette indisponible.');
                });
        }, 350);
    }

    var champAuteur = document.querySelector('#auteur');

    if (boutonSynopsis && champTitre && champResume) {
        boutonSynopsis.addEventListener('click', function () {
            var titre = champTitre.value.trim();

            if (titre === '') {
                afficherToast('Mets un titre avant');
                champTitre.focus();
                return;
            }

            boutonSynopsis.disabled = true;
            boutonSynopsis.textContent = 'Recherche...';

            fetch('synopsis.php?titre=' + encodeURIComponent(titre))
                .then(function (reponse) {
                    return reponse.json();
                })
                .then(function (data) {
                    if (!data.ok) {
                        afficherToast(data.message || 'Rien trouve');
                        return;
                    }

                    champResume.value = data.synopsis;
                    afficherToast('Synopsis trouve en francais');
                })
                .catch(function () {
                    afficherToast('Recherche impossible');
                })
                .finally(function () {
                    boutonSynopsis.disabled = false;
                    boutonSynopsis.textContent = 'Remplir le resume';
                });
        });
    }

    if (jaquettePreview && champTitre) {
        if (champAuteur) {
            champAuteur.addEventListener('input', rechercherJaquetteAuto);
        }

        champTitre.addEventListener('input', rechercherJaquetteAuto);
        rechercherJaquetteAuto();
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === '/' && recherche && document.activeElement !== recherche) {
            event.preventDefault();
            recherche.focus();
            afficherToast('Recherche rapide');
        }
    });

    var bouton = document.createElement('button');
    bouton.textContent = 'Top';
    bouton.className = 'bouton bouton-haut';
    document.body.appendChild(bouton);

    bouton.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    window.addEventListener('scroll', function () {
        var hauteur = document.documentElement.scrollHeight - window.innerHeight;
        var progression = hauteur > 0 ? (window.scrollY / hauteur) * 100 : 0;
        barre.style.width = progression + '%';

        if (window.scrollY > 500) {
            bouton.classList.add('visible');
        } else {
            bouton.classList.remove('visible');
        }
    });

    function ouvrirEntrySeventeen() {
        if (pageEntry) {
            return;
        }

        sessionStorage.setItem('entry_seventeen', '1');
        window.location.href = 'entry_seventeen.php';
    }

    if (document.querySelector('.recommandation-blame')) {
        setTimeout(function () {
            afficherToast('Blame! est recommande');
        }, 700);
    }

    var blame = document.querySelector('.carte-blame');

    if (blame) {
        setInterval(function () {
            blame.classList.add('pulse-js');
            setTimeout(function () {
                blame.classList.remove('pulse-js');
            }, 800);
        }, 9000);
    }

    var konami = [
        'ArrowUp',
        'ArrowUp',
        'ArrowDown',
        'ArrowDown',
        'ArrowLeft',
        'ArrowRight',
        'ArrowLeft',
        'ArrowRight',
        'b',
        'a'
    ];
    var konamiPosition = 0;
    var motGaster = 'gaster';
    var positionGaster = 0;
    var motTerminal = 'terminal';
    var positionTerminal = 0;

    function ouvrirTerminal() {
        if (document.querySelector('.terminal-cache')) {
            return;
        }
        var terminal = document.createElement('div');
        terminal.className = 'terminal-cache';
        terminal.innerHTML = '<div class="terminal-fenetre"><button type="button" class="terminal-fermer" aria-label="Fermer">x</button><p>ATLASIS.ME // TERMINAL</p><div class="terminal-sortie">Tape <strong>help</strong> pour voir les commandes.</div><form><span>&gt;</span><input type="text" autocomplete="off" autofocus></form></div>';
        document.body.appendChild(terminal);
        var champ = terminal.querySelector('input');
        var sortie = terminal.querySelector('.terminal-sortie');
        champ.focus();
        terminal.querySelector('.terminal-fermer').addEventListener('click', function () { terminal.remove(); });
        terminal.querySelector('form').addEventListener('submit', function (event) {
            event.preventDefault();
            var commande = champ.value.toLowerCase().trim();
            var reponses = {
                help: 'help, blame, catalogue, favoris, gaster, clear',
                blame: 'BLAME! : recommandation prioritaire. La megastructure ne s arrete jamais.',
                catalogue: 'Ouverture du catalogue...',
                favoris: 'Ouverture des favoris...',
                gaster: 'ENTRY NUMBER SEVENTEEN EST ACCESSIBLE EN TAPANT GASTER.',
                clear: ''
            };
            if (commande === 'catalogue') { window.location.href = 'catalogue.php'; return; }
            if (commande === 'favoris') { window.location.href = 'mes_favoris.php'; return; }
            sortie.textContent = reponses[commande] !== undefined ? reponses[commande] : 'Commande inconnue.';
            champ.value = '';
        });
    }

    function lancerAkuma() {
        var ancien = document.querySelector('.akuma-easter-egg');

        if (ancien) {
            ancien.remove();
        }

        var scene = document.createElement('div');
        scene.className = 'akuma-easter-egg';
        scene.innerHTML = '<div class="akuma-explosion"></div><div class="akuma-logo">&#22825;</div><div class="akuma-texte">AKUMA</div>';

        for (var i = 0; i < 20; i++) {
            var fragment = document.createElement('span');
            fragment.className = 'akuma-fragment';
            fragment.style.setProperty('--angle', (i * 18) + 'deg');
            fragment.style.setProperty('--distance', (90 + Math.random() * 160) + 'px');
            scene.appendChild(fragment);
        }

        document.body.appendChild(scene);
        document.body.classList.add('ecran-secoue');
        afficherToast('Konami Code active');

        setTimeout(function () {
            document.body.classList.remove('ecran-secoue');
        }, 650);

        setTimeout(function () {
            scene.remove();
        }, 2600);
    }

    document.addEventListener('keydown', function (event) {
        var touche = event.key.length === 1 ? event.key.toLowerCase() : event.key;
        var balise = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        var dansFormulaire = balise === 'input' || balise === 'textarea' || balise === 'select';

        if (!dansFormulaire && touche.length === 1) {
            if (touche === motGaster[positionGaster]) {
                positionGaster++;
            } else {
                positionGaster = touche === motGaster[0] ? 1 : 0;
            }

            if (positionGaster === motGaster.length) {
                positionGaster = 0;
                afficherToast('...');
                setTimeout(ouvrirEntrySeventeen, 450);
            }

            if (touche === motTerminal[positionTerminal]) {
                positionTerminal++;
            } else {
                positionTerminal = touche === motTerminal[0] ? 1 : 0;
            }

            if (positionTerminal === motTerminal.length) {
                positionTerminal = 0;
                ouvrirTerminal();
            }
        }

        if (touche === konami[konamiPosition]) {
            konamiPosition++;
        } else {
            konamiPosition = touche === konami[0] ? 1 : 0;
        }

        if (konamiPosition === konami.length) {
            konamiPosition = 0;
            lancerAkuma();
        }
    });
});
