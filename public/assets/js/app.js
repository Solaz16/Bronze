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

    document.querySelectorAll('.carte-livre').forEach(function (carte) {
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
            afficherToast(actif ? titre + ' ajoute aux favoris' : titre + ' retire des favoris');
        });

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
    });

    var recherche = document.querySelector('#recherche');
    var cartes = document.querySelectorAll('.carte-livre');
    var compteur = document.querySelector('.catalogue-entete p');

    function filtrerCartes() {
        if (!recherche || cartes.length === 0) {
            return;
        }

        var texte = recherche.value.toLowerCase().trim();
        var visibles = 0;

        cartes.forEach(function (carte) {
            var contenu = carte.textContent.toLowerCase();
            var visible = contenu.indexOf(texte) !== -1;
            carte.classList.toggle('cachee', !visible);

            if (visible) {
                visibles++;
            }
        });

        if (compteur) {
            compteur.textContent = visibles + ' manga(s) affiches';
        }
    }

    if (recherche && cartes.length > 0) {
        recherche.addEventListener('input', filtrerCartes);
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

    var tentativesBas = 0;
    var dernierBas = 0;
    var seuilEntry = 12;

    function estEnBas() {
        return window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4;
    }

    function ouvrirEntrySeventeen() {
        if (pageEntry) {
            return;
        }

        sessionStorage.setItem('entry_seventeen', '1');
        window.location.href = 'entry_seventeen.php';
    }

    window.addEventListener('wheel', function (event) {
        if (pageEntry || event.deltaY <= 0 || !estEnBas()) {
            return;
        }

        var maintenant = Date.now();

        if (maintenant - dernierBas > 4200) {
            tentativesBas = 0;
        }

        dernierBas = maintenant;
        tentativesBas++;

        if (tentativesBas === 7) {
            afficherToast('...');
        }

        if (tentativesBas >= seuilEntry) {
            ouvrirEntrySeventeen();
        }
    }, { passive: true });

    var dernierTouchY = 0;

    window.addEventListener('touchstart', function (event) {
        if (event.touches.length > 0) {
            dernierTouchY = event.touches[0].clientY;
        }
    }, { passive: true });

    window.addEventListener('touchmove', function (event) {
        if (pageEntry || event.touches.length === 0 || !estEnBas()) {
            return;
        }

        var y = event.touches[0].clientY;

        if (dernierTouchY - y > 26) {
            tentativesBas++;
            dernierTouchY = y;
        }

        if (tentativesBas === 7) {
            afficherToast('...');
        }

        if (tentativesBas >= seuilEntry) {
            ouvrirEntrySeventeen();
        }
    }, { passive: true });

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
