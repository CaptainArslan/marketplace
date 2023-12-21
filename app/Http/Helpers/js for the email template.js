let x =document.querySelector('#nicEditor0');
let emailEditor =document.querySelector('#email_template__');
xs = x.innerText;
emailEditor.insertAdjacentHTML('afterend', xs);
emailEditor.style.display = 'none';
let y = document.querySelector('#email_template__+ table');
y.classList.add('Email__Template__');
let imgSrc = 'https://www.chartjs.org/img/chartjs-logo.svg';
y.querySelector('img').src = imgSrc;
emailEditor.style.display = 'none';
//faizan script for email
