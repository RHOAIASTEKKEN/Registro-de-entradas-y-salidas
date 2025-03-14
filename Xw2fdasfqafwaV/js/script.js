document.addEventListener('DOMContentLoaded', function() {
    const sobre = document.querySelector('.sobre');
    const solapa = document.querySelector('.solapa');
  
    solapa.addEventListener('click', function() {
      sobre.classList.toggle('abierto');
    });
  
    window.addEventListener('scroll', function() {
      const scrollY = window.scrollY;
      const sobreTop = sobre.getBoundingClientRect().top;
  
      if (sobreTop <= window.innerHeight * 0.8) {
        sobre.classList.add('abierto');
      }
    });
  });
  