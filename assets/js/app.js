document.addEventListener('DOMContentLoaded', function () {
    var blocs = document.querySelectorAll('.bloc, .recommandation-blame');

    blocs.forEach(function (bloc, index) {
        bloc.classList.add('revele');
        setTimeout(function () {
            bloc.classList.add('visible');
        }, 80 + index * 80);
    });

    var ligneBlame = document.querySelector('.ligne-blame');

    if (ligneBlame) {
        ligneBlame.style.boxShadow = 'inset 4px 0 0 #4f9d8f';
    }

    var bouton = document.createElement('button');
    bouton.textContent = '↑';
    bouton.className = 'bouton bouton-haut';
    document.body.appendChild(bouton);

    bouton.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    window.addEventListener('scroll', function () {
        if (window.scrollY > 500) {
            bouton.classList.add('visible');
        } else {
            bouton.classList.remove('visible');
        }
    });
});
