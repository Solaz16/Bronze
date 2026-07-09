document.addEventListener('DOMContentLoaded', function () {
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

    if (document.querySelector('.recommandation-blame')) {
        setTimeout(function () {
            afficherToast('Blame! est recommande');
        }, 700);
    }
});
