const icon = document.getElementById('icon'); 
const nav = document.getElementById('nav'); 
const links = document.querySelectorAll('nav ul li a');

icon.addEventListener('click', function () {
    nav.classList.toggle('active'); 
});

links.forEach((link) => {
    link.addEventListener('click', function (event) {
        event.preventDefault(); 

        nav.classList.remove('active');

        const href = this.getAttribute('href'); 
        setTimeout(() => {
            window.location.href = href; 
        }, 500); 
    });
});

document.querySelectorAll('nav li a').forEach(item => {
    item.addEventListener('click', () => {
        document.querySelectorAll('nav li a').forEach(el => el.classList.remove('active'));
        
        item.classList.add('active');
    });
});