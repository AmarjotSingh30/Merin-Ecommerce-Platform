/* SIDEBAR COLLAPSE */
document.getElementById("hamburger").onclick = function(){
  document.getElementById("sidebar").classList.toggle("collapsed");
};

/* THEME SWITCH */
document.getElementById("themeToggle").onclick = function(){
  document.body.classList.toggle("dark");
};

/* PROFILE DROPDOWN */
document.getElementById("dropdownToggle").onclick = function(){
  document.getElementById("dropdownMenu").style.display =
    document.getElementById("dropdownMenu").style.display === "block"
    ? "none" : "block";
};

// -------- Portfolio Slider --------
let slides = document.querySelectorAll('.portfolio-slider .slide');
let currentSlide = 0;
document.getElementById('nextSlide').onclick = function() {
  slides[currentSlide].classList.remove('active');
  currentSlide = (currentSlide + 1) % slides.length;
  slides[currentSlide].classList.add('active');
};
document.getElementById('prevSlide').onclick = function() {
  slides[currentSlide].classList.remove('active');
  currentSlide = (currentSlide - 1 + slides.length) % slides.length;
  slides[currentSlide].classList.add('active');
};

// -------- To-Do List --------
const addBtn = document.getElementById('addTodo');
const input = document.getElementById('todoInput');
const list = document.getElementById('todoList');

addBtn.onclick = function() {
  if(input.value.trim() === '') return;
  let li = document.createElement('li');
  li.innerHTML = input.value + ' <button onclick="this.parentElement.remove()">Delete</button>';
  list.appendChild(li);
  input.value = '';
};



// vendors.php popup msg
// setTimeout(() => {
//   const toast = document.querySelector('.toast');
//   if (toast) toast.remove();
// }, 3000);
// vendors.php msg box end



